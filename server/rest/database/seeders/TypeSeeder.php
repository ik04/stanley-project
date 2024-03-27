<?php

namespace Database\Seeders;

use App\Models\CelestialType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $relativePath = __DIR__ . "/init/types.json";
        $types = file_get_contents($relativePath);
          $types = json_decode($types);
          $types = $types->types;
          foreach($types as $type){
            CelestialType::create([
                "type" => $type->type
            ]);
          }
    }
}
