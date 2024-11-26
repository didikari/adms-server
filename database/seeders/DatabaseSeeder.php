<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $users = [
            ['id' => 4, 'name' => 'Samiatun'],
            ['id' => 6, 'name' => 'Ali Saifudin'],
            ['id' => 7, 'name' => 'Awaludin Ridwan'],
            ['id' => 13, 'name' => 'Adi Rastono'],
            ['id' => 14, 'name' => 'Siti Nurkoidah'],
            ['id' => 15, 'name' => 'Desi Saraswati'],
            ['id' => 16, 'name' => 'Ahmad Khoiri'],
            ['id' => 17, 'name' => 'Rohmad Sholeh'],
            ['id' => 25, 'name' => 'Kartika Wulandari'],
            ['id' => 29, 'name' => 'Ahmad Fanani'],
            ['id' => 32, 'name' => 'Lia Nur Aini'],
            ['id' => 33, 'name' => 'Masrur Muzadi'],
            ['id' => 35, 'name' => 'Didik Ariyanto'],
            ['id' => 36, 'name' => 'Teguh Dwi Putra'],
            ['id' => 37, 'name' => 'Syakur'],
            ['id' => 38, 'name' => 'Kristiyoningsih'],
            ['id' => 40, 'name' => 'Khoirul Huda'],
            ['id' => 41, 'name' => 'Hamzah Nata Siswara'],
            ['id' => 42, 'name' => 'Vionita Tri Erida'],
            ['id' => 43, 'name' => 'Nurenik'],
            ['id' => 44, 'name' => 'Lisa Dwifani Indarwati'],
            ['id' => 45, 'name' => 'Zainurrohim'],
            ['id' => 46, 'name' => 'Ah. Maftuh Hafidh Zuhdi'],
            ['id' => 47, 'name' => 'Afsah Indah Maulidah'],
            ['id' => 48, 'name' => 'Noname'],
            ['id' => 49, 'name' => 'Rihlatin Nur Endi Rohmah'],
        ];

        foreach ($users as $user) {
            // Generate email
            $baseEmail = strtolower(str_replace(' ', '', explode(' ', $user['name'])[0])) . '@example.com';
            $email = $baseEmail;

            // Tambahkan angka jika email sudah ada
            $counter = 1;
            while (User::where('email', $email)->exists()) {
                $email = strtolower(str_replace(' ', '', explode(' ', $user['name'])[0])) . $counter . '@example.com';
                $counter++;
            }

            // Simpan atau perbarui data pengguna
            User::updateOrCreate(
                ['id' => $user['id']],
                [
                    'name' => $user['name'],
                    'email' => $email,
                    'password' => bcrypt('password'),
                ]
            );
        }
    }
}
