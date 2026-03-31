<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicineLookupSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('medicines_lookup')->insert([
            ['medicine_name' => 'Paracetamol 500mg Tablet', 'category' => 'Analgesic'],
            ['medicine_name' => 'Paracetamol 250mg/5mL Syrup', 'category' => 'Analgesic'],
            ['medicine_name' => 'Amoxicillin 500mg Capsule', 'category' => 'Antibiotic'],
            ['medicine_name' => 'Amoxicillin 250mg/5mL Suspension', 'category' => 'Antibiotic'],
            ['medicine_name' => 'Losartan 50mg Tablet', 'category' => 'Antihypertensive'],
            ['medicine_name' => 'Amlodipine 5mg Tablet', 'category' => 'Antihypertensive'],
            ['medicine_name' => 'Metformin 500mg Tablet', 'category' => 'Antidiabetic'],
        ]);

    }
}
