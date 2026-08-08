<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //FAKER PARA AUTOGENERAR CODIGO DE BARRAS RANDOM DE 13 DIGITOS
            'codigo'=>$this->faker->unique()->ean13(),
            //GENERA UN STRING DE 5 PALABRAS
            'descripcion'=>$this->faker->words(5,true),


        ];
    }
}
