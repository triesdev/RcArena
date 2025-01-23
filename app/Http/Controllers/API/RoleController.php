<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends ApiController
{
    public function index(Request $request)
    {
        $data = Role::paginate($request->per_page ?? 10);

        return $this->successResponse("Success", $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first(), $validator->errors());
        }

        $data = Role::create($request->all());

        return $this->successResponse("Success", $data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first(), $validator->errors());
        }

        $data = Role::find($id);

        if (!$data) {
            return $this->notFoundResponse();
        }

        $data->update($request->all());

        return $this->successResponse("Success", $data);
    }

    public function destroy($id)
    {
        $data = Role::find($id);

        if (!$data) {
            return $this->notFoundResponse();
        }

        $data->delete();
        return $this->successResponse("Deleted");
    }
}
