<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
   public function run(): void
    {
        $totalRegistros = 30000;
        $tamanoLote = 500;

        // Inicia la barra de progreso en la consola
        $this->command->getOutput()->progressStart($totalRegistros);

        for ($i = 0; $i < $totalRegistros; $i += $tamanoLote) {
            $loteProductos = [];

            for ($j = 0; $j < $tamanoLote; $j++) {
                $loteProductos[] = [
                    // Usamos el helper fake() nativo
                    'codigo' => fake()->unique()->ean13(),
                    'descripcion' => fake()->words(5, true),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insertamos el lote completo de una sola vez
            \Illuminate\Support\Facades\DB::table('productos')->insert($loteProductos);
            
            // Avanzamos la barra de progreso
            $this->command->getOutput()->progressAdvance($tamanoLote);
        }

        // Finaliza la barra visualmente
        $this->command->getOutput()->progressFinish();
        $this->command->info('¡Los 30.000 productos han sido creados con éxito!');
    }
}
