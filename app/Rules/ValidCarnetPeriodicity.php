<?php

namespace App\Rules;

use App\Models\Types\CarnetPeriodicity;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCarnetPeriodicity implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $validValues = CarnetPeriodicity::values();

        if (!in_array(ucfirst(strtolower($value)), $validValues)) {
            $fail('O campo :attribute deve ser ' . implode(' ou ', $validValues));
        }
    }
}
