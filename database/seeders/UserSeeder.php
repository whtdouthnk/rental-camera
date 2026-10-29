<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'nama'     => 'Admin RentCam',
                'email'    => 'admin@rentcam.test',
                'password' => 'password',
                'no_hp'    => '081234567890',
                'role'     => 'Admin',
                'status'   => 'Aktif',
            ],
            [
                'nama'     => 'Petugas RentCam',
                'email'    => 'petugas@rentcam.test',
                'password' => 'password',
                'no_hp'    => '081234567891',
                'role'     => 'Petugas',
                'status'   => 'Aktif',
            ],
            [
                'nama'     => 'Pengguna RentCam',
                'email'    => 'pengguna@rentcam.test',
                'password' => 'password',
                'no_hp'    => '081234567892',
                'role'     => 'Pengguna',
                'status'   => 'Aktif',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
