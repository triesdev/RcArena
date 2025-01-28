<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\ApiController;
use App\Models\Menu;
use App\Models\MenuRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuRoleController extends ApiController
{
    public function index(Request $request)
    {
        $data = Menu::orderBy('order')->whereIn('type', ['menu', 'submenu'])->get();
        $menu_role = MenuRole::where('role_id', $request->role_id)
            ->get();

        foreach ($data as $item) {
            $item['get'] = $menu_role->where('menu_id', $item->id)->where('method', 'GET')->first();
            $item['show'] = $menu_role->where('menu_id', $item->id)->where('method', 'SHOW')->first();
            $item['post'] = $menu_role->where('menu_id', $item->id)->where('method', 'POST')->first();
            $item['put'] = $menu_role->where('menu_id', $item->id)->where('method', 'PUT')->first();
            $item['del'] = $menu_role->where('menu_id', $item->id)->where('method', 'DEL')->first();
        }

        $tree = $this->buildTree($data);

        return $this->successResponse("Success", $tree);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'menu_id' => 'required',
            'role_id' => 'required',
            'method_access' => 'required|in:GET,SHOW,POST,PUT,DEL',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first(), $validator->errors());
        }

        $menu_role = MenuRole::where('menu_id', $request->menu_id)->where('role_id', $request->role_id)->where('method', $request->method_access)->first();
        if ($menu_role) {
            return $this->successResponse("Success", $menu_role);
        }

        $data = MenuRole::create($request->all());

        return $this->successResponse("Success", $data);
    }

    public function destroy(Request $request, $id)
    {
        $data = MenuRole::find($id)->delete();

        return $this->successResponse("Success", $data);
    }

    public function menuTree(Request $request)
    {
        $auth_user = $request->auth_user;
        $menu_ids = MenuRole::where('role_id', $auth_user->role_id)->where('method', 'GET')->pluck('menu_id');

        $menu = Menu::orderBy('order')->whereIn('id', $menu_ids)->get();

        // build tree
        $tree = $this->buildTree($menu);

        return $this->successResponse("Successs", $tree);
    }

    private function buildTree($menu)
    {
        $tree = [];
        foreach ($menu as $item) {
            if (!$item->parent_id) {
                $item['children'] = $menu->where('parent_id', $item->id)->flatten();
                $tree[] = $item;
            }
        }
        return $tree;
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'menu_id' => 'required',
            'role_id' => 'required',
            'method_access' => 'required|in:GET,SHOW,POST,PUT,DEL',
            'action' => 'required|in:add,delete',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first(), $validator->errors());
        }

        if ($request->action == 'add') {
            $menu_role = MenuRole::where('menu_id', $request->menu_id)->where('role_id', $request->role_id)->where('method', $request->method_access)->first();
            if ($menu_role) {
                return $this->successResponse("Success");
            } else {
                MenuRole::create([
                    'menu_id' => $request->menu_id,
                    'role_id' => $request->role_id,
                    'method' => $request->method_access,
                ]);
                return $this->successResponse("Success");
            }
        }

        if ($request->action == 'delete') {
            MenuRole::where('menu_id', $request->menu_id)->where('role_id', $request->role_id)->where('method', $request->method_access)->delete();
            return $this->successResponse("Success");
        }

        return $this->successResponse("Success");
    }
}
