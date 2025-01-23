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
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@ra.com',
                'password' => bcrypt('password'),
                'role_id' => 2,
                'type' => 'cms',
                'type_mobile' => null,
                'user_code' => NULL,
            ],
            [
                'name' => 'Coor 1',
                'email' => 'coor1@ra.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'type' => 'mobile',
                'type_mobile' => 'coordinator',
                'user_code' => '930KKK',
            ],
            [
                'name' => 'User 1',
                'email' => 'user1@ra.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'type' => 'mobile',
                'type_mobile' => 'regular',
                'user_code' => '4930KF',
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
                ]);
            }
        }
    }

    private function Roles()
    {
        $data = [
            [
                'name' => 'Superadmin',
            ],
            [
                'name' => 'Admin',
            ],
            [
                'name' => 'Coordinator',
            ],
            [
                'name' => 'User',
            ],
        ];

        foreach ($data as $item) {
            $created = Role::whereName($item['name'])->first();
            if ($created) {
                $created->update([
                    'name' => $item['name'],
                ]);
            } else {
                Role::create([
                    'name' => $item['name'],
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
