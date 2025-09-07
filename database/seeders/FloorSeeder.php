<?php

namespace Database\Seeders;

use App\Models\Floor;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FloorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Floor::create(['name' => 'Lantai 1']);
        Floor::create(['name' => 'Lantai 2']);
        Floor::create(['name' => 'Lantai 3']);
    }
}
