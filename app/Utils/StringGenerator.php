<?php

namespace App\Utils;

class StringGenerator {
    /**
     * Available character sets for string generation
     */
    private const LOWERCASE = 'abcdefghijklmnopqrstuvwxyz';
    private const UPPERCASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const NUMBERS = '0123456789';
    private const SPECIAL = '!@#$%^&*()_+-=[]{}|;:,.<>?';

    /**
     * Generate a random string with specified options
     *
     * @param int $length Length of the string to generate
     * @param array $options Array of options (lowercase, uppercase, numbers, special)
     * @return string Generated random string
     * @throws Exception If no character set is selected or length is invalid
     */
    public static function generate(
        int $length = 10,
        array $options = ['lowercase' => true, 'uppercase' => true, 'numbers' => true, 'special' => false]
    ): string {
        if ($length < 1) {
            throw new \Exception('Length must be greater than 0');
        }

        $characters = '';

        if ($options['lowercase'] ?? false) {
            $characters .= self::LOWERCASE;
        }
        if ($options['uppercase'] ?? false) {
            $characters .= self::UPPERCASE;
        }
        if ($options['numbers'] ?? false) {
            $characters .= self::NUMBERS;
        }
        if ($options['special'] ?? false) {
            $characters .= self::SPECIAL;
        }

        if (empty($characters)) {
            throw new \Exception('At least one character set must be selected');
        }

        $result = '';
        $charactersLength = strlen($characters);

        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $result;
    }

    /**
     * Generate a random string suitable for passwords
     *
     * @param int $length Password length
     * @return string Generated password
     */
    public static function generatePassword(int $length = 12): string {
        return self::generate($length, [
            'lowercase' => true,
            'uppercase' => true,
            'numbers' => true,
            'special' => true
        ]);
    }

    /**
     * Generate a random alphanumeric string
     *
     * @param int $length String length
     * @return string Generated string
     */
    public static function generateAlphanumeric(int $length = 10): string {
        return self::generate($length, [
            'lowercase' => true,
            'uppercase' => true,
            'numbers' => true,
            'special' => false
        ]);
    }

    /**
     * Generate a random hex string
     *
     * @param int $length Desired length (will be doubled for hex representation)
     * @return string Generated hex string
     */
    public static function generateHex(int $length = 16): string {
        return bin2hex(random_bytes($length));
    }

    /**
     * Generate a URL-safe random string
     *
     * @param int $length String length
     * @return string Generated URL-safe string
     */
    public static function generateUrlSafe(int $length = 32): string {
        return rtrim(strtr(base64_encode(random_bytes($length)), '+/', '-_'), '=');
    }
}
