<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $carnet_id
 * @property Carbon $due_on
 * @property int $number
 * @property float $value
 * @property bool $down_payment
 */
class Installment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'carnet_id',
        'due_on',
        'number',
        'value',
        'down_payment',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_on' => 'date:Y-m-d',
            'down_payment' => 'bool',
        ];
    }

    /**
     * Model's "value" attribute caster.
     *
     * @return Attribute
     */
    protected function value(): Attribute
    {
        return Attribute::make(
            get: fn(int|float $value) => round(($value / 100), 2),
            set: fn(int|float $value) => $value * 100
        );
    }
}
