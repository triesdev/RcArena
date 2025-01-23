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
        $this->Menus();
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

    private function Menus()
    {
//        $data = [
//            [
//                "type" => "title",
//                "name" => "accountancy",
//                "url" => "#",
//                "title" => "Akuntansi",
//            ],
//            [
//                "type" => "menu",
//                "name" => "dashboard",
//                "url" => "dashboard",
//                "title" => "Dashboard",
//            ],
//            [
//                "type" => "menu",
//                "name" => "transactions",
//                "url" => "transactions",
//                "title" => "Transaksi",
//            ],
//            [
//                "type" => "menu",
//                "name" => "purchases",
//                "url" => "#",
//                "title" => "Aset",
//                "submenu" => [
//                    [
//                        "type" => "menu",
//                        "name" => "purchases-add",
//                        "url" => "purchases-add",
//                        "title" => "Tambah Baru",
//                    ],
//                    [
//                        "type" => "menu",
//                        "name" => "purchases-data",
//                        "url" => "purchases-data",
//                        "title" => "Data Aset",
//                    ],
//                    [
//                        "type" => "menu",
//                        "name" => "purchases-data",
//                        "url" => "purchases-data",
//                        "title" => "Data Aset",
//                    ],
//                ]
//            ],
//            [
//                "type" => "menu",
//                "name" => "cut-off",
//                "url" => "cut-off",
//                "title" => "Cut Off",
//            ],
//            [
//                "type" => "menu",
//                "name" => "reports",
//                "url" => "#",
//                "title" => "Laporan",
//                "submenu" => [
//                    [
//                        "type" => "menu",
//                        "name" => "balance-sheet",
//                        "url" => "balance-sheet",
//                        "title" => "Neraca",
//                    ],
//                    [
//                        "type" => "menu",
//                        "name" => "profit-loss",
//                        "url" => "profit-loss",
//                        "title" => "Laba Rugi",
//                    ],
//                    [
//                        "type" => "menu",
//                        "name" => "worksheet",
//                        "url" => "worksheet",
//                        "title" => "Buku Besar",
//                    ],
//                ]
//            ],
//            // Data Master
//            [
//                "type" => "title",
//                "name" => "data_master",
//                "url" => "#",
//                "title" => "Data Master",
//            ],
//            [
//                "type" => "menu",
//                "name" => "accounts",
//                "url" => "accounts",
//                "title" => "Data Akun",
//            ],
//            [
//                "type" => "menu",
//                "name" => "subaccounts",
//                "url" => "subaccounts",
//                "title" => "Data Subakun",
//            ],
//            [
//                "type" => "menu",
//                "name" => "users",
//                "url" => "users",
//                "title" => "Admin",
//            ],
//            [
//                "type" => "menu",
//                "name" => "roles",
//                "url" => "roles",
//                "title" => "Roles",
//            ],
//            [
//                "type" => "menu",
//                "name" => "user-role",
//                "url" => "user-role",
//                "title" => "User Role",
//            ],
//        ];
//
//        foreach ($data as $item) {
//            $created = Menu::whereName($item['name'])->first();
//            if ($created) {
//                $created->update([
//                    'type' => $item['type'],
//                    'title' => $item['title'],
//                    'order' => $this->order,
//                ]);
//                $this->order++;
//
//                if (isset($item['submenu'])) {
//                    $this->InsertUpdateSubmenu($created, $item['submenu']);
//                }
//            } else {
//                $menu = Menu::create([
//                    "parent_id" => 0,
//                    "type" => $item['type'],
//                    "name" => $item['name'],
//                    "title" => $item['title'],
//                    "url" => $item['url'],
//                    "icon" => "",
//                    'order' => $this->order,
//                ]);
//                $this->order++;
//
//                if (isset($item['submenu'])) {
//                    $this->InsertUpdateSubmenu($menu, $item['submenu']);
//                }
//            }
//        }
    }

    private function InsertUpdateSubmenu($menu, $submenu)
    {
//        foreach ($submenu as $item) {
//            $created = Menu::whereName($item['name'])->first();
//            if ($created) {
//                $created->update([
//                    'type' => $item['type'],
//                    'title' => $item['title'],
//                    'order' => $this->order,
//                ]);
//            } else {
//                Menu::create([
//                    "parent_id" => $menu->id,
//                    "type" => $item['type'],
//                    "name" => $item['name'],
//                    "title" => $item['title'],
//                    "url" => $item['url'],
//                    "icon" => "",
//                    'order' => $this->order,
//                ]);
//            }
//            $this->order++;
//        }
    }

    private function MenuRole()
    {
//        $methods = [
//            "GET",
//            "SHOW",
//            "POST",
//            "PUT",
//            "DEL",
//        ];
//
//        $menus = Menu::all();
//
//        // Role 1
//        foreach ($methods as $method) {
//            foreach ($menus->pluck('id')->toArray() as $menu) {
//                $created = MenuRole::whereMenuId($menu)->whereRoleId(1)->whereMethod($method)->first();
//                if ($created) {
//                    $created->update([
//                        "method" => $method,
//                    ]);
//                    continue;
//                }
//
//                MenuRole::create([
//                    "menu_id" => $menu,
//                    "role_id" => 1,
//                    "method" => $method,
//                ]);
//            }
//        }
//
//        // Role 2
//        $allow_transaction = [
//            'transactions'
//        ];
//
//        foreach ($methods as $method) {
//            foreach ($menus as $menu) {
//                if (!in_array($menu['name'], $allow_transaction)) {
//                    continue;
//                }
//
//                $created = MenuRole::whereMenuId($menu)->whereRoleId(2)->whereMethod($method)->first();
//                if ($created) {
//                    $created->update([
//                        "method" => $method,
//                    ]);
//                    continue;
//                }
//
//                MenuRole::create([
//                    "menu_id" => $menu['id'],
//                    "role_id" => 2,
//                    "method" => $method,
//                ]);
//            }
//        }
    }
}
