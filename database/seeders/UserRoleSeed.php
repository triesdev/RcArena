<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRoleSeed extends Seeder
{
    private $order = 0;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->Roles();
        $this->MenuRole();
        $this->User();
    }

    private function User()
    {
        $data = [
            [
                'name' => 'Superadmin',
                'email' => 'superadmin@ra.com',
                'password' => bcrypt('password'),
                'role_id' => 1,
                'type' => 'cms',
                'type_mobile' => null,
                'user_code' => NULL,
                'api_token' => NULL
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@ra.com',
                'password' => bcrypt('password'),
                'role_id' => 2,
                'type' => 'cms',
                'type_mobile' => null,
                'user_code' => NULL,
                'api_token' => NULL
            ],
            [
                'name' => 'Coor 1',
                'email' => 'coor1@ra.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'type' => 'mobile',
                'type_mobile' => 'coordinator',
                'user_code' => '930KKK',
                'api_token' => '654321'
            ],
            [
                'name' => 'User 1',
                'email' => 'user1@ra.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'type' => 'mobile',
                'type_mobile' => 'regular',
                'user_code' => '4930KF',
                'api_token' => '123456'
            ],
        ];

        foreach ($data as $item) {
            $created = User::whereEmail($item['email'])->first();
            if ($created) {
                $created->update([
                    'name' => $item['name'],
                    'email' => $item['email'],
                    'password' => $item['password'],
                    'role_id' => $item['role_id'],
                    'user_type' => $item['type'],
                    'user_type_mobile' => $item['type_mobile'],
                    'user_code' => $item['user_code'],
                    'api_token' => $item['api_token']
                ]);
            } else {
                User::create([
                    'name' => $item['name'],
                    'email' => $item['email'],
                    'password' => $item['password'],
                    'role_id' => $item['role_id'],
                    'user_type' => $item['type'],
                    'user_type_mobile' => $item['type_mobile'],
                    'user_code' => $item['user_code'],
                    'api_token' => $item['api_token']
                ]);
            }
        }
    }

    private function Roles()
    {
        $data = [
            [
                'name' => 'Superadmin',
                'is_default' => 0,
                'type' => 'cms'
            ],
            [
                'name' => 'Admin',
                'is_default' => 0,
                'type' => 'cms'
            ],
            [
                'name' => 'Coordinator',
                'is_default' => 0,
                'type' => 'mobile'
            ],
            [
                'name' => 'User',
                'is_default' => 1,
                'type' => 'mobile'
            ],
        ];

        foreach ($data as $item) {
            $created = Role::whereName($item['name'])->first();
            if ($created) {
                $created->update([
                    'name' => $item['name'],
                    'is_default' => $item['is_default'],
                    'type' => $item['type']
                ]);
            } else {
                Role::create([
                    'name' => $item['name'],
                    'is_default' => $item['is_default'],
                    'type' => $item['type']
                ]);
            }
        }
    }

    private function MenuRole()
    {
        $methods = [
            "GET",
            "SHOW",
            "POST",
            "PUT",
            "DEL",
        ];

        $menus = Menu::all();

        // Role 1
        foreach ($methods as $method) {
            foreach ($menus->pluck('id')->toArray() as $menu) {
                $created = MenuRole::whereMenuId($menu)->whereRoleId(1)->whereMethod($method)->first();
                if ($created) {
                    $created->update([
                        "method" => $method,
                    ]);
                    continue;
                }

                MenuRole::create([
                    "menu_id" => $menu,
                    "role_id" => 1,
                    "method" => $method,
                ]);
            }
        }

        // Role 2
        $allow_transaction = [
            'transactions'
        ];

        foreach ($methods as $method) {
            foreach ($menus as $menu) {
                if (!in_array($menu['name'], $allow_transaction)) {
                    continue;
                }

                $created = MenuRole::whereMenuId($menu)->whereRoleId(2)->whereMethod($method)->first();
                if ($created) {
                    $created->update([
                        "method" => $method,
                    ]);
                    continue;
                }

                MenuRole::create([
                    "menu_id" => $menu['id'],
                    "role_id" => 2,
                    "method" => $method,
                ]);
            }
        }
    }
}
