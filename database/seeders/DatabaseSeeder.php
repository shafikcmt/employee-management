<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Designation;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['admin@example.com', 'Admin User', 'admin'],
            ['hr@example.com', 'HR Accounts', 'hr'],
            ['viewer@example.com', 'Viewer User', 'viewer'],
        ] as $user) {
            User::updateOrCreate(
                ['email' => $user[0]],
                ['name' => $user[1], 'password' => Hash::make('password'), 'role' => $user[2]]
            );
        }

        foreach (['Faena Hotel', 'Six Senses', 'Amansamar', 'Polo Club', 'WADI SAFAR', 'FAENA - Cairo', 'Faena SubContract - Masafat'] as $name) {
            Project::firstOrCreate(['name' => $name], ['status' => 'active']);
        }

        foreach (['Masafat', 'Cairo', 'Subcontractor'] as $name) {
            Company::firstOrCreate(['name' => $name], ['type' => 'subcontractor']);
        }

        foreach (['Mason', 'Carpenter', 'Steel Fixer', 'Helper', 'Electrician', 'Plumber', 'Foreman', 'Driver', 'Painter'] as $name) {
            Designation::firstOrCreate(['name' => $name]);
        }

        $this->call(EmployeeReferenceSeeder::class);
    }
}
