<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends ApiController
{
    public function index(Request $request)
    {
        return response()->json([
            'message' => 'Success',
            'data' => []
        ]);
    }

    public function getEventHome()
    {
        try {
            $events = Event::select("id","image_uri")->whereIsActive(1)->get();
            return $this->successResponse("Successs", $events);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage());
        }
    }

    public function getEventDetail($id){
        try {
            $event = Event::with(["eventClass" =>  function ($q){
                return $q->with("tickets");
            }])->find($id);
            return $this->successResponse("Successs", $event);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage());
        }
    }

    public function store(Request $request)
    {
        return response()->json([
            'message' => 'Success',
            'data' => $request->all()
        ]);
    }

    public function update($id, Request $request)
    {
        return response()->json([
            'message' => 'Success',
            'data' => $request->all()
        ]);
    }

    public function destroy($id)
    {
        return response()->json([
            'message' => 'Success',
            'data' => []
        ]);
    }
}
