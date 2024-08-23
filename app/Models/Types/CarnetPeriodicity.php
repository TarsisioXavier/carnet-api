<?php

namespace App\Models\Types;

enum CarnetPeriodicity: string
{
    case Monthly = 'Mensal';
    case Weekly = 'Semanal';

    /**
     * Get all values from enum.
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(static::cases(), 'value');
    }

    /**
     * Tries to find the enum value treating the string case.
     *
     * @return CarnetPeriodicity
     */
    public static function fromCaseInsensitive(string $value): CarnetPeriodicity
    {
        return static::tryFrom(ucfirst(strtolower($value)));
    }
}
