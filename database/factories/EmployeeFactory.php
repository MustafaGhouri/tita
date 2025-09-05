<?php


namespace Database\Factories;

use App\Models\Employee;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            // same-company seeding: ya to random existing company, ya nayi factory
            'company_id' => Company::inRandomOrder()->value('id') ?? Company::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name'  => $this->faker->lastName(),
            'email'      => $this->faker->unique()->safeEmail(),
            'position'   => $this->faker->randomElement(['Sales','Customer Service','Marketing']),
            'status'     => $this->faker->randomElement(['ACTIVE','INACTIVE']),
        ];
    }
}
