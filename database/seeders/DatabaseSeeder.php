<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = ['admin', 'staff', 'dosen', 'ob', 'satpam'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $users = [
            ['name' => 'Ihsyaluddin', 'finger_id' => 1, 'role' => 'dosen'],
            ['name' => 'M. Ismail', 'finger_id' => 2, 'role' => 'dosen'],
            ['name' => 'Mapena Tuban', 'finger_id' => 3, 'role' => 'admin'],
            ['name' => 'Samiatun', 'finger_id' => 4, 'role' => 'staff'],
            ['name' => 'Theo M. S', 'finger_id' => 5, 'role' => 'dosen'],
            ['name' => 'Ali Saifudin', 'finger_id' => 6, 'role' => 'dosen'],
            ['name' => 'Awaludin Ridwan', 'finger_id' => 7, 'role' => 'dosen'],
            ['name' => 'Aline Sisi H.', 'finger_id' => 8, 'role' => 'dosen'],
            ['name' => 'Yuni Susanti H.', 'finger_id' => 9, 'role' => 'dosen'],
            ['name' => 'Aliyah', 'finger_id' => 10, 'role' => 'dosen'],
            ['name' => 'Refa Firgiyanto', 'finger_id' => 11, 'role' => 'dosen'],
            ['name' => 'Nur Setiawati', 'finger_id' => 12, 'role' => 'dosen'],
            ['name' => 'Adi Rastono', 'finger_id' => 13, 'role' => 'dosen'],
            ['name' => 'Siti Nurkoidah', 'finger_id' => 14, 'role' => 'staff'],
            ['name' => 'Desi Saraswati', 'finger_id' => 15, 'role' => 'staff'],
            ['name' => 'Ahmad Khoiri', 'finger_id' => 16, 'role' => 'staff'],
            ['name' => 'Rohmad Sholeh', 'finger_id' => 17, 'role' => 'staff'],
            ['name' => 'Siti Aslimah', 'finger_id' => 18, 'role' => 'dosen'],
            ['name' => 'Ide Risentito', 'finger_id' => 19, 'role' => 'dosen'],
            ['name' => 'Syahrul G Sukmaya', 'finger_id' => 20, 'role' => 'dosen'],
            ['name' => 'Pitri Ratna Asih', 'finger_id' => 21, 'role' => 'dosen'],
            ['name' => 'Ega Faustina', 'finger_id' => 22, 'role' => 'dosen'],
            ['name' => 'Dita Megasari', 'finger_id' => 25, 'role' => 'dosen'],
            ['name' => 'Retna Dewi Lestari', 'finger_id' => 26, 'role' => 'dosen'],
            ['name' => 'Kartika Wulandari', 'finger_id' => 27, 'role' => 'dosen'],
            ['name' => 'Andre Meiditama Kasenta', 'finger_id' => 28, 'role' => 'dosen'],
            ['name' => 'Ir. Sigit Budi, IH', 'finger_id' => 36, 'role' => 'dosen'],
            ['name' => 'Eny Sholikhatin', 'finger_id' => 37, 'role' => 'dosen'],
            ['name' => 'Ahmad Fanani', 'finger_id' => 38, 'role' => 'dosen'],
            ['name' => 'Dwi Putri Sunaryanti', 'finger_id' => 39, 'role' => 'dosen'],
            ['name' => 'Dr. Kristiawan, S.P., MM', 'finger_id' => 40, 'role' => 'dosen'],
            ['name' => 'Lia Nur Aini', 'finger_id' => 41, 'role' => 'dosen'],
            ['name' => 'Masrur Muzadi', 'finger_id' => 42, 'role' => 'dosen'],
            ['name' => 'Wenny Amaliah', 'finger_id' => 43, 'role' => 'dosen'],
            ['name' => 'Didik Ariyanto', 'finger_id' => 44, 'role' => 'staff'],
            ['name' => 'Teguh Dwi Putra', 'finger_id' => 45, 'role' => 'dosen'],
            ['name' => 'Syakur', 'finger_id' => 46, 'role' => 'staff'],
            ['name' => 'Kristiyoningsih', 'finger_id' => 47, 'role' => 'dosen'],
            ['name' => 'Khoirul Anam', 'finger_id' => 48, 'role' => 'staff'],
            ['name' => 'Khoirul Huda', 'finger_id' => 49, 'role' => 'dosen'],
            ['name' => 'Hamzah Nata Siswara', 'finger_id' => 50, 'role' => 'dosen'],
            ['name' => 'Vionita Tri Erida', 'finger_id' => 51, 'role' => 'staff'],
            ['name' => 'Nurenik', 'finger_id' => 52, 'role' => 'dosen'],
            ['name' => 'Lisa Dwifani Indarwati', 'finger_id' => 53, 'role' => 'dosen'],
            ['name' => 'Zainurrohim', 'finger_id' => 54, 'role' => 'satpam'],
            ['name' => 'Ah. Maftuh Hafidh Zuhdi', 'finger_id' => 55, 'role' => 'dosen'],
            ['name' => 'Afsah Indah Maulidah', 'finger_id' => 56, 'role' => 'dosen'],
            ['name' => 'Noname', 'finger_id' => 57, 'role' => 'staff'],
            ['name' => 'Rihlatin Nur Endi Rohmah', 'finger_id' => 58, 'role' => 'staff'],
        ];

        foreach ($users as $user) {
            $baseEmail = strtolower(str_replace(' ', '', explode(' ', $user['name'])[0])) . '@example.com';
            $email = $baseEmail;

            $counter = 1;
            while (User::where('email', $email)->exists()) {
                $email = strtolower(str_replace(' ', '', explode(' ', $user['name'])[0])) . $counter . '@example.com';
                $counter++;
            }

            $userRecord = User::updateOrCreate(
                [
                    'name' => $user['name'],
                    'email' => $email,
                    'password' => bcrypt('password'),
                    'finger_id' => $user['finger_id']
                ]
            );

            $userRecord->assignRole($user['role']);
        }
    }
}
