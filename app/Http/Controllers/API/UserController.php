<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends ApiController
{
    public function index(Request $request)
    {
        $data = User::when($request->role_id, function ($query) use ($request) {
                $query->where('role_id', $request->role_id);
            })
            ->when($request->name, function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%');
            })
            ->paginate($request->per_page ?? 10);

        return $this->successResponse("Success", $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first(), $validator->errors());
        }

        $data = User::create($request->all());

        return $this->successResponse("Success", $data);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            "phone_number" => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first(), $validator->errors());
        };

        $data = User::find($id);

        if (!$data) {
            return $this->notFoundResponse();
        }

        $data->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'image_uri' => null,
        ]);

        return $this->successResponse("Success", $data);
    }

    public function destroy($id)
    {
        $data = User::find($id);

        if (!$data) {
            return $this->notFoundResponse();
        }

        if ($data->role_id != 1) {
            $data->delete();
            return $this->successResponse("Deleted");
        }
    }

//    public function show($id, Request $request)
//    {
//        $data = User::find($request->auth_user->id);
//
//        if (!$data) {
//            return $this->notFoundResponse();
//        }
//
//        return $this->successResponse("Success", $data);
//    }
}
