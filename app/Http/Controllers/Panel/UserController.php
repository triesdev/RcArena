<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\ApiController;
use App\Models\Role;
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
                $query->where('users.name', 'like', '%' . $request->name . '%');
            })
            ->when($request->role, function ($query) use ($request) {
                $role_ids = [];
                if($request->role == 'admin'){
                    $env_admin = env('ROLE_ADMIN');
                    $role_ids = explode(",", $env_admin);
                } else if ($request->role == 'user'){
                    $env_user = env('ROLE_USER');
                    $role_ids = explode(",", $env_user);
                }

                if(count($role_ids) > 0){
                    error_log(json_encode($role_ids));
                    return $query->whereIn('role_id', $role_ids);
                }
            })
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->select('users.*', 'roles.name as role_name')
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

        $request->merge(['password' => bcrypt($request->password)]);
        $data = User::create($request->except("password_confirmation", "auth_user"));

        return $this->successResponse("Success", $data);
    }

    public function show($id)
    {
        $data = User::find($id);
        return $this->successResponse("Success", $data);
    }

    public function getByCode($code)
    {
        $data = User::where('user_code', $code)->first();

        if (!$data) {
            return $this->notFoundResponse();
        }

        return $this->successResponse("Success", $data);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone_number' => 'required|numeric|digits_between:10,15',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first(), $validator->errors());
        };

        $data = User::find($id);

        if (!$data) {
            return $this->notFoundResponse();
        }

        $role_id_default_mobile = Role::whereType('mobile')->whereIsDefault(1)->first()->id;

        $data->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'role_id' => $role_id_default_mobile,
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

        return $this->errorResponse("delete failed");
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
