<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::create([
            'name' => 'Lucia',
            'surname' => 'Cofini',
            'email' => 'lucia.cofini@contaq.it',
            'password' => Hash::make('password'),
            // 'extension' => 1002
        ]);

        $user->assignRole('Admin');


        $user = User::create([
            'name' => 'Carla',
            'surname' => 'Rianna',
            'email' => 'carla.rianna@contaq.it',
            'password' => Hash::make('password'),
            // 'extension' => 1000
        ]);

        $user->assignRole('Admin');

        $user = User::create([
            'name' => 'Ludovica',
            'surname' => 'Ferrara',
            'email' => 'ludovica.ferrara@contaq.it',
            'password' => Hash::make('password'),
            // 'extension' => 1001
        ]);

        $user->assignRole('Admin');

        // $skills = [
        //     0 => [],
        //     1 => [1,2],
        //     2 => [1],
        // ];

        $operatorsCSV = fopen(base_path("database/data/ops_ayvens.csv"), "r");

        
        while (($data = fgetcsv($operatorsCSV, 2000, ";")) !== FALSE) {
            $user = User::create([
                'name' => ucfirst(strtolower($data[0])),
                'surname' => ucfirst(strtolower($data[1])),
                'email' => $data[2],
                'password' => Hash::make('password'),
            ]);    
            $user->assignRole('Staff');
            // $user->skills()->sync($skills[rand(0,2)]);
        }
        
        // User::factory()->count(70)->create()->each(function ($user) use ($skills){
        //     $user->assignRole('operatore');
        //     $user->skills()->sync($skills[rand(0,2)]);
        // });
    }
}