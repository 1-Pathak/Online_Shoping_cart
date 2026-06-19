<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@example.com');
        $password = env('ADMIN_PASSWORD', 'password');

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->usertype = 'ADM';
            $user->password = Hash::make($password);
            $user->save();
        } else {
            User::create([
                'name' => env('ADMIN_NAME', 'Administrator'),
                'email' => $email,
                'mobile' => env('ADMIN_MOBILE', '9999999999'),
                'password' => Hash::make($password),
                'usertype' => 'ADM',
            ]);
        }
    }
}
