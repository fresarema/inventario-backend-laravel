<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RutValido implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Limpia puntos y guiones del input original
        $rut = preg_replace('/[^kK0-9]/i', '', $value);
        
        if (strlen($rut) < 8) {
            $fail('El :attribute no tiene un formato de RUT válido.');
            return;
        }

        $cuerpo = substr($rut, 0, -1);
        $dvDigitado = strtoupper(substr($rut, -1));
        
        $suma = 0;
        $factor = 2;
        
        for ($i = strlen($cuerpo) - 1; $i >= 0; $i--) {
            $suma += $factor * $cuerpo[$i];
            $factor = $factor % 7 == 0 ? 2 : $factor + 1;
        }
        
        $dvEsperado = 11 - ($suma % 11);
        $dvEsperado = $dvEsperado == 11 ? '0' : ($dvEsperado == 10 ? 'K' : (string)$dvEsperado);

        if ($dvDigitado !== $dvEsperado) {
            $fail('El :attribute ingresado no es válido o no existe.');
        }
    }
}