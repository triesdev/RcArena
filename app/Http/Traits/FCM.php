<?php

namespace App\Http\Traits;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Log;

trait FCM {

    protected $client;
    protected $projectId;
    protected $base_url;

    public function checkBaseUrl()
    {
        return $this->base_url;
    }

    public function getAccessToken()
    {
        $this->projectId = env('FCM_PROJECT_ID');
        $this->base_url = 'https://fcm.googleapis.com/v1/projects/' . $this->projectId .'/';

        $credentialsPath = storage_path('service-account.json');  // Use storage_path helper

        if (!file_exists($credentialsPath)) {
            throw new \Exception("Service account file does not exist at path: " . $credentialsPath);
        }

        $credentials = new ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/firebase.messaging'],
            $credentialsPath,
        );

        $credentials->fetchAuthToken();
        return $credentials->getLastReceivedToken();
    }

    /**
     * Send Notification to a Single FCM Token
     */
    public function sendNotif($fcmToken = "", $data = []) {

        $response = [
            'status' => false,
            'text' => "",
        ];

        // Validation
        if (!$fcmToken) {
            $response['text'] = "FCM token cannot be empty";
            return $response;
        }

        if (!isset($data['title'])) {
            $response['text'] = "Notification title cannot be empty";
            return $response;
        }

        if (!isset($data['message'])) {
            $response['text'] = "Notification message cannot be empty";
            return $response;
        }

        $data['route'] = $data['route'] ?? "/main_home";
        $data['id'] = $data['id'] ?? 0;

        // Generate Access Token
        $accessToken = $this->getAccessToken()['access_token'];

        $payload = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $data['title'],
                    'body' => $data['message'],
                ],
                'data' => [
                    'page_route' => $data['route'],
                    'id' => (string) $data['id'],
                ]
            ]
        ];

        // Send the request
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/'.$this->projectId.'/messages:send');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer '.$accessToken,
            ];

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $res = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);

            $response['status'] = true;
            $response['text'] = "Success";
            $response['response'] = $res;
            $response['payload'] = $payload;
            $response['header'] = $headers;

        } catch (\Exception $e) {
            $response['text'] = $e->getMessage();
        }

        return $response;
    }

    /**
     * Send Notification to Multiple FCM Tokens
     */
    public function sendMultipleNotif($fcmTokens, $data = []) {
        $response = [
            'status' => false,
            'text' => "",
            'responses' => []
        ];

        // Validation
        if (!is_array($fcmTokens) || count($fcmTokens) == 0) {
            $response['text'] = "FCM Tokens must be a non-empty array";
            return $response;
        }

        if (!isset($data['title'])) {
            $response['text'] = "Notification title cannot be empty";
            return $response;
        }

        if (!isset($data['message'])) {
            $response['text'] = "Notification message cannot be empty";
            return $response;
        }

        $data['route'] = $data['route'] ?? "/main_home";
        $data['id'] = $data['id'] ?? 0;

        // Generate Access Token
        $accessToken = $this->getAccessToken()['access_token'];

        // Prepare multi-cURL
        $multiCurl = curl_multi_init();
        $curlHandles = [];
        $fcmUrl = 'https://fcm.googleapis.com/v1/projects/' . $this->projectId . '/messages:send';

        foreach ($fcmTokens as $token) {
            // Create individual payload for each token
            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $data['title'],
                        'body' => $data['message'],
                    ],
                    'data' => [
                        'page_route' => $data['route'],
                        'id' => (string) $data['id'],
                    ]
                ]
            ];

            // Initialize cURL handle for each request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $fcmUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken,
            ];

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // Add the handle to the multi-cURL handle
            curl_multi_add_handle($multiCurl, $ch);
            $curlHandles[] = $ch;
        }

        // Execute all requests in parallel
        $running = null;
        do {
            curl_multi_exec($multiCurl, $running);
            curl_multi_select($multiCurl);
        } while ($running > 0);

        // Collect responses from each cURL handle
        foreach ($curlHandles as $ch) {
            $res = curl_multi_getcontent($ch);
            if (curl_errno($ch)) {
                $response['responses'][] = [
                    'token' => $token,
                    'error' => curl_error($ch)
                ];
            } else {
                $response['responses'][] = [
                    'token' => $token,
                    'response' => $res
                ];
            }
            // Remove the handle from the multi-handle and close it
            curl_multi_remove_handle($multiCurl, $ch);
            curl_close($ch);
        }

        // Close the multi-cURL handle
        curl_multi_close($multiCurl);

        $response['status'] = true;
        $response['text'] = "Notifications sent";

        return $response;
    }


    /**
     * Send Notification to a Topic
     */
    public function sendNotifByTopic($topic = "cusgeneral", $data = []) {
        $response = [
            'status' => false,
            'text' => "",
        ];

        // Validation
        if (!$topic) {
            $response['text'] = "Topic cannot be empty";
            return $response;
        }

        if (!isset($data['title'])) {
            $response['text'] = "Notification title cannot be empty";
            return $response;
        }

        if (!isset($data['message'])) {
            $response['text'] = "Notification message cannot be empty";
            return $response;
        }

        $data['route'] = $data['route'] ?? "/main_home";
        $data['id'] = $data['id'] ?? 0;

        // Generate Access Token
        $accessToken = $this->getAccessToken()['access_token'];

        // Set up the request headers and payload
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        $payload = [
            'message' => [
                'topic' => $topic,
                'notification' => [
                    'title' => $data['title'],
                    'body' => $data['message'],
                ],
                'data' => [
                    'page_route' => $data['route'],
                    'id' => (string) $data['id'],
                ]
            ]
        ];

        // Send the request using cURL
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/'.$this->projectId.'/messages:send');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $responseBody = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new \Exception('cURL Error: ' . curl_error($ch));
            }

            curl_close($ch);

            $response['status'] = true;
            $response['text'] = "Success";
            $response['response'] = $responseBody;

            Log::info($response);
        } catch (\Exception $e) {
            $response['text'] = $e->getMessage();
        }

        return $response;
    }
}
