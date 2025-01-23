<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\ApiController;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuController extends ApiController
{
    public function index(Request $request)
    {
        $data = Menu::when($request->name, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->name . '%');
        })
            ->paginate($request->per_page ?? 10);

        return $this->successResponse("Success", $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'url' => 'required',
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first(), $validator->errors());
        }

        $data = Menu::create($request->all());

        return $this->successResponse("Success", $data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'url' => 'required',
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first(), $validator->errors());
        }

        $data = Menu::find($id);

        if (!$data) {
            return $this->notFoundResponse();
        }

        $data->update($request->all());

        return $this->successResponse("Success", $data);
    }

    public function destroy($id)
    {
        $data = Menu::find($id);

        if (!$data) {
            return $this->notFoundResponse();
        }

        $data->delete();
        return $this->successResponse("Deleted");
    }
}
