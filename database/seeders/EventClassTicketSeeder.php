<?php

namespace Database\Seeders;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class EventClassTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    protected $event;
    protected $class;

    public function run(): void
    {
        $this->createEvents();
        $this->createClass();
        $this->tickets();
        $this->bundleTickets();
    }

    private function createEvents()
    {

        $event = DB::table('events')->insert([
            'user_organizer_id' => 1,
            'created_by_user_id' => 1,
            'name' => 'Sample Event',
            'image_uri' => 'https://example.com/image.jpg',
            'description' => 'lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua ut enim ad minim veniam quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur excepteur sint occaecat cupidatat non proident sunt in culpa qui officia deserunt mollit anim id est laborum',
            'event_launch_at' => Carbon::now(),
            'ticket_purchasing_at' => Carbon::now(),
            'location_name' => 'Sample Location',
            'location_address' => '123 Sample Street, Sample City',
            'event_date' => Carbon::now()->toDateString(),
            'event_start' => Carbon::now()->addHours(10),
            'event_end' => Carbon::now()->addHours(12),
            'schedules' => '
                <ul>
                    <li>Sample Jadwal 1</li>
                    <li>Sample Jadwal 2</li>
                    <li>Sample Jadwal 3</li>
                    <li>Sample Jadwal 4</li>
                    <li>Sample Jadwal 5</li>
                </ul>
            ',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'deleted_at' => null,
        ]);

        $this->event = Event::first();
    }

    private function createClass()
    {
        $event = $this->event;

        DB::table('classes')->insert([
            [
                'event_id' => $event->id,
                'event_name' => $event->name,
                'name' => "Sample Class 1",
                'price' => 250000,
                'reward' => "<ul>
                    <li>Juara 1: Rp10,000,000</li>
                    <li>Juara 2: Rp7,500,000</li>
                    <li>Juara 3: Rp5,000,000</li>
                    <li>Juara 4: Rp2,500,000</li>
                    <li>Juara 5: Rp1,000,000</li>
                </ul>",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ],
            [
                'event_id' => $event->id,
                'event_name' => $event->name,
                'name' => "Sample Class 2",
                'price' => 300000,
                'reward' => "<ul>
                    <li>Juara 1: Rp12,000,000</li>
                    <li>Juara 2: Rp9,000,000</li>
                    <li>Juara 3: Rp6,000,000</li>
                    <li>Juara 4: Rp3,000,000</li>
                    <li>Juara 5: Rp1,500,000</li>
                </ul>",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ],
            [
                'event_id' => $event->id,
                'event_name' => $event->name,
                'name' => "Sample Class 3",
                'price' => 200000,
                'reward' => "<ul>
                    <li>Juara 1: Rp8,000,000</li>
                    <li>Juara 2: Rp6,000,000</li>
                    <li>Juara 3: Rp4,000,000</li>
                    <li>Juara 4: Rp2,000,000</li>
                    <li>Juara 5: Rp800,000</li>
                </ul>",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ],
        ]);

        $this->class = DB::table("classes")->where('event_id', $event->id)->get();
    }

    private function tickets()
    {
        $clasess = $this->class;

        foreach ($clasess as $class){
            // Create Ticket
            $tickets = DB::table('tickets')->insert([
                [
                    'class_id' => $class->id,
                    'event_id' => $class->event_id,

                    'name' => "Ticket {$class->name} 1",
                    'price' => 25000,
                    'quota' => 100,
                    'quota_left' => 100,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'deleted_at' => null,
                    'ticket_type' => 'REGULAR',
                ],
                [
                    'class_id' => $class->id,

                    'event_id' => $class->event_id,
                    'name' => "Ticket {$class->name} 2",
                    'price' => 30000,
                    'quota' => 100,
                    'quota_left' => 100,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'deleted_at' => null,
                    'ticket_type' => 'REGULAR',
                ],
                [
                    'class_id' => $class->id,

                    'event_id' => $class->event_id,
                    'name' => "Ticket {$class->name} 3",
                    'price' => 20000,
                    'quota' => 100,
                    'quota_left' => 100,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'deleted_at' => null,
                    'ticket_type' => 'REGULAR',
                ],
            ]);
        }
    }


    private function bundleTickets()
    {
        // Create Ticket Bundle
        DB::table('ticket_bundles')->insert([
            [
                'event_id' => $this->event->id,
                'name' => "Bundle {$this->event->name} 1",
                'price' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ],
        ]);

        $ticket_bundle = DB::table('ticket_bundles')->where('event_id', $this->event->id)->first();

        $ticket_bundle_id = $ticket_bundle->id;

        // Get 2 Class and 1 Ticket
        $class = DB::table('classes')->where('event_id', $this->event->id)->limit(2)->get();

        $bundle_price = 0;
        foreach ($class as $val){
            $ticket = DB::table('tickets')->where('class_id', $val->id)->first();

            // Update
            DB::table('tickets')->where('id', $ticket->id)->update([
                'ticket_bundle_id' => $ticket_bundle_id
            ]);

            $bundle_price += $ticket->price;
        }

        DB::table('ticket_bundles')->where('id', $ticket_bundle_id)->update([
            'price' => $bundle_price
        ]);
    }
}
