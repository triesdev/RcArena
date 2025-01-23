<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuRole;
use Illuminate\Database\Seeder;

class MenuSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'title'    => "Dashboard",
                'icon'     => "bi-columns-gap",
                'url'      => "/panel/dashboard",
                'children' => [],
                'type'     => "menu",
            ],
            [
                'title'    => "Transactions",
                'icon'     => "bi-list-check",
                'url'      => "/panel/transactions--",
                'type'     => "menu",
                'children' => [
                    [
                        'title' => "Data",
                        'icon'  => "",
                        'url'   => "/panel/transactions",
                        'type'  => "submenu",
                    ],
                ]
            ],

            [
                'title'    => "Master Data",
                'icon'     => "",
                'url'      => "/panel/master-data--",
                'type'     => "title",
                'children' => []
            ],
            [
                'title'    => "Event",
                'icon'     => "bi-camera-video",
                'url'      => "/panel/events--",
                'type'     => "menu",
                'children' => [
                    [
                        'title' => "Tambah Baru",
                        'icon'  => "",
                        'url'   => "/panel/events/add",
                        'type'  => "submenu",
                    ],
                    [
                        'title' => "Data",
                        'icon'  => "",
                        'url'   => "/panel/events",
                        'type'  => "submenu",
                    ],
                ]
            ],
            [
                'title'    => "User",
                'icon'     => "bi-shield-check",
                'url'      => "/panel/users--",
                'type'     => "menu",
                'children' => [
                    [
                        'title' => "Tambah Baru",
                        'icon'  => "",
                        'url'   => "/panel/users/add",
                        'type'  => "submenu",
                    ],
                    [
                        'title' => "Data",
                        'icon'  => "",
                        'url'   => "/panel/users",
                        'type'  => "submenu",
                    ],
                ]
            ],
            [
                'title'    => "Role",
                'icon'     => "bi-bezier2",
                'url'      => "/panel/roles--",
                'type'     => "menu",
                'children' => [
                    [
                        'title' => "Tambah Baru",
                        'icon'  => "",
                        'url'   => "/panel/roles/add",
                        'type'  => "submenu",
                    ],
                    [
                        'title' => "Data",
                        'icon'  => "",
                        'url'   => "/panel/roles",
                        'type'  => "submenu",
                    ],
                ]
            ],
            [
                'title'    => "Menu",
                'icon'     => "bi-menu-button-wide",
                'url'      => "/panel/menus--",
                'type'     => "menu",
                'children' => [
                    [
                        'title' => "Tambah Baru",
                        'icon'  => "",
                        'url'   => "/panel/menus/add",
                        'type'  => "submenu",
                    ],
                    [
                        'title' => "Data",
                        'icon'  => "",
                        'url'   => "/panel/menus",
                        'type'  => "submenu",
                    ],
                    [
                        'title' => "Menu Role",
                        'icon'  => "",
                        'url'   => "/panel/menu-role",
                        'type'  => "submenu",
                    ],
                ]
            ],

        ];

        $active_ids = [];
        foreach ($data as $key => $datum) {
            $menu_created = Menu::whereUrl($datum['url'])->first();
            if (!$menu_created) {
                $menu_created = Menu::create([
                    "order" => $key + 1,
                    "title" => $datum['title'],
                    "icon"  => $datum['icon'],
                    "url"   => $datum['url'],
                    "name"  => $datum['url'],
                    "type"  => $datum['type'],
                ]);
            } else {
                $menu_created->update([
                    "order" => $key + 1,
                    "title" => $datum['title'],
                    "icon"  => $datum['icon'],
                    "url"   => $datum['url'],
                    "name"  => $datum['url'],
                    "type"  => $datum['type'],
                ]);
            }
            $active_ids[] = $menu_created->id;

            if (count($datum['children']) > 0) {
                foreach ($datum['children'] as $cld => $child) {
                    $child_created = Menu::whereUrl($child['url'])->first();
                    if (!$child_created) {
                        $child_created = Menu::create([
                            "order"     => $cld + 1,
                            "title"     => $child['title'],
                            "icon"      => $child['icon'],
                            "url"       => $child['url'],
                            "name"      => $child['url'],
                            "type"      => $child['type'],
                            "parent_id" => $menu_created['id'],
                        ]);
                    } else {
                        $child_created->update([
                            "order"     => $cld + 1,
                            "title"     => $child['title'],
                            "icon"      => $child['icon'],
                            "url"       => $child['url'],
                            "name"      => $child['url'],
                            "type"      => $child['type'],
                            "parent_id" => $menu_created['id'],
                        ]);
                    }
                    $active_ids[] = $child_created->id;
                }
            }
        }

        Menu::whereNotIn('id', $active_ids)->delete();

        $menus = Menu::get();
        foreach ($menus as $mn) {
            $menu_role = MenuRole::whereMenuId($mn['id'])->whereRoleId(1)->first();
            if (!$menu_role) {
                MenuRole::create([
                    'menu_id' => $mn['id'],
                    'role_id' => 1,
                ]);
            }
        }
    }
}
