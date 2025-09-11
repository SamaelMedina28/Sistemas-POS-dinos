<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creando tipos de servicios
        Type::create(['name' => 'Grande', 'price' => 200.00, 'minutes' => 10]);
        Type::create(['name' => 'Mediano', 'price' => 140.00, 'minutes' => 10]);
        Type::create(['name' => 'Chico', 'price' => 100.00, 'minutes' => 15]);
        Type::create(['name' => 'Foto', 'price' => 50.00, 'description' => 'Se toma una foto con varios dinosaurios.']);
    }
}
