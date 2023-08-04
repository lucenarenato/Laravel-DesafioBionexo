<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userCount = User::count();

        if ($userCount === 0) {
            User::factory()->create([
                'name'  => 'Renato Lucena',
                'email' => 'renato.lucena@bionexo.com',
                'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
                'islocked' => false,
            ]);
            echo "O modelo User está vazio.";
        } else {
            echo "O modelo User não está vazio.";
        }
    }
}
