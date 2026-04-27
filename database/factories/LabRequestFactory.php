<?php

namespace Database\Factories;

use App\Models\Consultation;
use App\Models\HealthWorker;
use App\Models\LabRequest;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LabRequest>
 */
class LabRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'consultation_id' => Consultation::factory(),
            'requested_by' => HealthWorker::factory(),
            'lab_test_name' => $this->faker->randomElement([
                'Complete Blood Count',
                'Urinalysis',
                'Fecalysis',
                'Blood Chemistry',
                'Chest X-Ray',
                'Pregnancy Test',
                'Blood Typing',
            ]),
            'lab_test_description' => $this->faker->optional(0.7)->sentence(),
            'status' => $this->faker->randomElement(['pending', 'completed', 'cancelled']),
            'requested_date' => $this->faker->date(),
            'completed_date' => $this->faker->optional(0.5)->date(),
            'results' => $this->faker->optional(0.6)->paragraph(),
            'notes' => $this->faker->optional(0.4)->sentence(),
        ];
    }
}
