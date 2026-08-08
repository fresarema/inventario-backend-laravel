<?php

namespace Database\Factories;

use App\Models\Estudiante;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EstudianteFactory extends Factory
{
    protected $model = Estudiante::class;

    public function definition()
    {
        return [
			'run' => fake()->name(),
			'nombres' => fake()->name(),
			'apellidos' => fake()->name(),
			'f_nac' => fake()->name(),
			'carrera_id' => fake()->name(),
			'foto' => fake()->name(),
			'estado' => fake()->name(),
        ];
    }
}
