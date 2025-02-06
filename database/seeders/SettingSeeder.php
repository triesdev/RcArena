<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
    [
        "key" => "payment_guide",
        "value" => "
                    <ol>
                        <li>Pilih Metode Pembayaran antara lain:
                            <ul>
                                <li>Bank BCA</li>
                                <li>QRIS</li>
                            </ul>
                        </li>
                        <li>Untuk memudahkan admin dalam proses pengecekan transaksi, silahkan transfer sesuai dengan Sub Total beserta kode unik yang tertera.</li>
                        <li>Setelah melakukan pembayaran silahkan klik tombol konfirmasi pembayaran dan upload bukti pembayaran.</li>
                        <li>Setelah pembayaran terkonfirmasi, maka e-ticket akan terbit dan akan tampil di halaman tiket anda.</li>
                    </ol>
                ",
            ],
        ];

        foreach ($settings as $setting) {
            // Check if the setting already exists
            if (!Setting::where('key', $setting['key'])->exists()) {
                Setting::create($setting);
            }
        }
    }
}
