<?php

namespace App\Models;

use App\Models\Installment;
use App\Models\Types\CarnetPeriodicity;
use App\Observers\CarnetObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $value
 * @property int $installments_count
 * @property Carbon $first_due_date
 * @property string $periodicity
 * @property bool $down_payment
 * @property Collection<Installment> $installments
 */
#[ObservedBy([CarnetObserver::class])]
class Carnet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'value',
        'installments_count',
        'first_due_date',
        'periodicity',
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
            'first_due_date' => 'date:Y-m-d',
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

    /**
     * Model's "value" attribute caster.
     *
     * @return Attribute
     */
    protected function periodicity(): Attribute
    {
        return Attribute::make(
            get: function (string|CarnetPeriodicity $value) {
                if ($value instanceof CarnetPeriodicity) {
                    return $value;
                }

                return CarnetPeriodicity::from($value);
            },
            set: function (string|CarnetPeriodicity $value) {
                if ($value instanceof CarnetPeriodicity) {
                    return $value;
                }

                return CarnetPeriodicity::fromCaseInsensitive($value);
            },
        );
    }

    /**
     * Installments of the carnet.
     *
     * @return HasMany
     */
    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class);
    }

    /**
     * Identify if the carnet have a down payment.
     *
     * @return bool
     */
    public function hasDownPayment(): bool
    {
        return $this->down_payment > 0;
    }

    /**
     * Gives the value of each installment considerating
     * the down payment installment.
     *
     * @return float
     */
    public function calculateInstallmentValue(): float
    {
        $installmentsQuantity = $this->installments_count;
        if ($this->hasDownPayment()) {
            $installmentsQuantity--;
        }

        $installmentValue = ($this->value - $this->down_payment) / $installmentsQuantity;

        return round($installmentValue, 2);
    }

    /**
     * Make arrays with data for creations of Installments.
     *
     * @return array
     */
    public function spreadIntoInstallments(): array
    {
        $installments = [];

        if ($this->hasDownPayment()) {
            $this->makeDownPaymentInstallment($installments);
        }

        $this->makeInstallments($installments);

        // Fixin recurring decimal issue...
        $installments = collect($installments);

        $totalDiff = round($this->value - $installments->sum('value'), 2);
        if ($totalDiff > 0) {
            $installment = $installments->pop(1);
            $installment['value'] += $totalDiff;

            $installments->add($installment);
        }

        return $installments->all();
    }

    /**
     * Make the data array for the Installment of "down payment".
     *
     * @param  array  &$installments
     *
     * @return void
     */
    protected function makeDownPaymentInstallment(array &$installments): void
    {
        $installments[] = [
            'due_on' => now()->format('Y-m-d'),
            'number' => 1,
            'value' => $this->down_payment,
            'down_payment' => true,
        ];
    }

    /**
     * Make the data array for Installments besides the down payment installment.
     *
     * @param  array  &$installments
     *
     * @return void
     */
    protected function makeInstallments(array &$installments): void
    {
        $dueDate = $this->first_due_date;
        $installmentValue = $this->calculateInstallmentValue();

        for ($i = (count($installments) + 1); $i <= $this->installments_count; $i++) {
            $installments[] = [
                'due_on' => $dueDate->format('Y-m-d'),
                'number' => $i,
                'value' => $installmentValue,
                'down_payment' => false,
            ];

            if ($this->periodicity == CarnetPeriodicity::Monthly) {
                $dueDate->addMonth();
            } else {
                $dueDate->addWeek();
            }
        }
    }
}
