<?php

namespace Database\Seeders;

use App\Models\Floor;
use App\Models\Table;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $floors = Floor::all();
        foreach ($floors as $floor) {
            for ($i = 1; $i <= 24; $i++) {
                Table::create([
                    'number' => $i,
                    'status' => 'available',
                    'floor_id' => $floor->id,
                ]);
            }
        }
    }
}
