<?php

namespace Tests\Feature\Carnet;

use App\Models\Carnet;
use App\Models\Installment;
use App\Models\Types\CarnetPeriodicity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreCarnetTest extends TestCase
{
    use RefreshDatabase;

    public function test_carnet_can_be_stored(): void
    {
        $payload = [
            'valor_total' => fake()->numberBetween(100, 1000),
            'qtd_parcelas' => 3,
            'data_primeiro_vencimento' => now()->addMonth(),
            'periodicidade' => CarnetPeriodicity::Monthly->value,
        ];

        $response = $this->postJson(route('api.v1.carnet.store'), $payload);

        $response->assertCreated();

        $lastInstallmentValue = round($payload['valor_total'] / 3, 2);
        $diff = $payload['valor_total'] - ($lastInstallmentValue * 3);
        if (round($diff, 2) > 0) {
            $lastInstallmentValue += $diff;
        }

        $response->assertJson([
            'total' => $payload['valor_total'],
            'valor_entrada' => 0,
            'parcelas' => [
                [
                    'data_vencimento' => now()->addMonths(1)->format('Y-m-d'),
                    'valor' => round($payload['valor_total'] / 3, 2),
                    'numero' => 1,
                    'entrada' => false,
                ],
                [
                    'data_vencimento' => now()->addMonths(2)->format('Y-m-d'),
                    'valor' => round($payload['valor_total'] / 3, 2),
                    'numero' => 2,
                    'entrada' => false,
                ],
                [
                    'data_vencimento' => now()->addMonths(3)->format('Y-m-d'),
                    'valor' => $lastInstallmentValue,
                    'numero' => 3,
                    'entrada' => false,
                ],
            ],
        ]);

        $this->assertDatabaseCount(Carnet::class, 1);
        $dbCarnet = Carnet::first();
        $this->assertSame([
            'value' => (float) $payload['valor_total'],
            'installments_count' => $payload['qtd_parcelas'],
            'first_due_date' => $payload['data_primeiro_vencimento']->format('Y-m-d'),
            'periodicity' => $payload['periodicidade'],
            'down_payment' => 0,
        ], [
            'value' => $dbCarnet->value,
            'installments_count' => $dbCarnet->installments_count,
            'first_due_date' => $dbCarnet->first_due_date->format('Y-m-d'),
            'periodicity' => $dbCarnet->periodicity->value,
            'down_payment' => $dbCarnet->down_payment,
        ]);

        $this->assertDatabaseCount(Installment::class, 3);
    }

    public function test_carnet_sum_of_installments(): void
    {
        $payload = [
            'valor_total' => 100,
            'qtd_parcelas' => 3,
            'data_primeiro_vencimento' => now()->addMonth(),
            'periodicidade' => CarnetPeriodicity::Monthly->value,
        ];

        $response = $this->postJson(route('api.v1.carnet.store'), $payload);

        $response->assertCreated();

        $dbInstallments = Installment::all();

        $dbInstallmentsTotal = $dbInstallments->sum('value');

        $this->assertEquals($payload['valor_total'], $dbInstallmentsTotal);
    }

    public function test_due_date_respects_weakly_payments(): void
    {
        $payload = [
            'valor_total' => 100,
            'qtd_parcelas' => 4,
            'data_primeiro_vencimento' => now()->addWeek()->format('Y-m-d'),
            'periodicidade' => CarnetPeriodicity::Weekly,
        ];

        $response = $this->postJson(route('api.v1.carnet.store'), $payload);

        $response->assertCreated();

        $response->assertJson([
            'total' => $payload['valor_total'],
            'valor_entrada' => 0,
            'parcelas' => [
                [
                    'data_vencimento' => now()->addWeeks(1)->format('Y-m-d'),
                    'valor' => 25,
                    'numero' => 1,
                    'entrada' => false,
                ],
                [
                    'data_vencimento' => now()->addWeeks(2)->format('Y-m-d'),
                    'valor' => 25,
                    'numero' => 2,
                    'entrada' => false,
                ],
                [
                    'data_vencimento' => now()->addWeeks(3)->format('Y-m-d'),
                    'valor' => 25,
                    'numero' => 3,
                    'entrada' => false,
                ],
                [
                    'data_vencimento' => now()->addWeeks(4)->format('Y-m-d'),
                    'valor' => 25,
                    'numero' => 4,
                    'entrada' => false,
                ],
            ],
        ]);
    }

    /**
     * Divisão de R$ 100,00 em 12 Parcelas:
     */
    public function test_mandatory_scenario_1(): void
    {
        $payload = [
            'valor_total' => 100.00,
            'qtd_parcelas' => 12,
            'data_primeiro_vencimento' => '2024-08-01',
            'periodicidade' => 'mensal',
        ];

        $response = $this->postJson(route('api.v1.carnet.store'), $payload);

        $response->assertCreated();

        $carnet = Carnet::first();

        $this->assertEquals(100, $carnet->installments->sum('value'));
    }

    /**
     * Divisão de R$ 0,30 em 2 Parcelas com Entrada de R$ 0,10
     */
    public function test_mandatory_scenario_2(): void
    {
        $payload = [
            'valor_total' => 0.30,
            'qtd_parcelas' => 2,
            'data_primeiro_vencimento' => '2024-08-01',
            'periodicidade' => 'semanal',
            'valor_entrada' => 0.10,
        ];

        $response = $this->postJson(route('api.v1.carnet.store'), $payload);

        $response->assertCreated();

        $carnet = Carnet::first();

        $this->assertEquals(2, $carnet->installments->count());
        $this->assertEquals(0.30, round($carnet->installments->sum('value'), 2));
    }
}
