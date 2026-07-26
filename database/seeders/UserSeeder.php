<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin (Grade 6)
        User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrator KPI',
                'nik' => '100001',
                'email' => 'admin@kpi.go.id',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'grade_level' => 6,
            ]
        );

        // 2. Operator A (Grade 2)
        User::firstOrCreate(
            ['username' => 'operator.a'],
            [
                'name' => 'Budi Santoso (Operator A)',
                'nik' => '202607',
                'email' => 'operatora@kpi.go.id',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'grade_level' => 2,
            ]
        );

        // 3. Operator B (Grade 3)
        User::firstOrCreate(
            ['username' => 'operator.b'],
            [
                'name' => 'Siti Rahma (Operator B)',
                'nik' => '202608',
                'email' => 'operatorb@kpi.go.id',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'grade_level' => 3,
            ]
        );

        // 4. Pimpinan / Management (Grade 6)
        User::firstOrCreate(
            ['username' => 'management'],
            [
                'name' => 'Ir. Ahmad Wijaya (Pimpinan Management)',
                'nik' => '300001',
                'email' => 'pimpinan@kpi.go.id',
                'password' => Hash::make('password'),
                'role' => 'management',
                'grade_level' => 6,
            ]
        );
    }
}
