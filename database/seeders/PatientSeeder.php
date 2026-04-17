<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        // Common Filipino Names
        $lastNames = ['Santos', 'Batoon', 'Dumdum', 'Laurel', 'Chavaria', 'Coquilla', 'Velasquez', 'Torres', 'de la Torre', 'Gonzales', 'Rivera', 'Castro'];
        $firstNamesMale = ['Charles', 'John', 'James', 'Michael', 'David', 'Rafael', 'Joseph', 'Luis', 'Carlos', 'Eduardo'];
        $firstNamesFemale = ['Shem', 'Zyrel', 'Ronalyn', 'Roselmae', 'Sheanna', 'Marivic', 'Mary Ann', 'Maria', 'Anna Leah', 'Josefina'];
        
        $patients = [];
        $households = [];

        // Generate 100 Dummy Patients
        for ($i = 0; $i < 100; $i++) {
            
            // 1. Create a Household First (Required by database schema)
            $householdId = DB::table('households')->insertGetId([
                'zone_id' => rand(1, 8), // Random Zone 1-8 (since we only have 8 zones)
                'family_name_head' => $lastNames[array_rand($lastNames)],
                'contact_number' => '09'.rand(100000000, 999999999),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Create the Patient
            $isMale = rand(0, 1);
            $firstName = $isMale ? $firstNamesMale[array_rand($firstNamesMale)] : $firstNamesFemale[array_rand($firstNamesFemale)];
            $lastName = $lastNames[array_rand($lastNames)];

            // Random birth date between 1950 and 2023
            $birthDate = Carbon::createFromDate(rand(1950, 2023), rand(1, 12), rand(1, 28));

            $patients[] = [
                'household_id' => $householdId,
                'last_name' => $lastName,
                'first_name' => $firstName,
                'middle_name' => $lastNames[array_rand($lastNames)], // Just pick another last name as middle
                'suffix' => $isMale && rand(0, 5) === 0 ? 'Jr.' : null, // Occasional Jr.
                'sex' => $isMale ? 'Male' : 'Female',
                'date_of_birth' => $birthDate->format('Y-m-d'),
                'birth_place' => 'Sta. Ana, Tagoloan',
                'blood_type' => ['A+', 'B+', 'O+', 'AB+'][rand(0, 3)],
                'civil_status' => rand(0, 1) ? 'Single' : 'Married',
                'educational_attainment' => 'High School Graduate',
                'employment_status' => 'Unemployed',
                'has_4ps' => rand(0, 1),
                'has_nhts' => rand(0, 1),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('patients')->insert($patients);
    }
}
