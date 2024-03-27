<?php

namespace Database\Seeders;

use App\Models\CelestialObject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ObjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $relativePath = __DIR__ . "/init/objects.json";
        $objects = file_get_contents($relativePath);
          $objects = json_decode($objects);
          $objects = $objects->objects;
          foreach($objects as $object){
            CelestialObject::create([
                "name" => $object->name,
                "type_id" => $object->type_id
            ]);
          }
    }
}
