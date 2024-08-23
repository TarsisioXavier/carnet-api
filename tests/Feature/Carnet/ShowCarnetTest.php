<?php

namespace Tests\Feature\Carnet;

use App\Models\Carnet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowCarnetTest extends TestCase
{
    use RefreshDatabase;

    public function test_carnet_can_be_shown()
    {
        $carnet = Carnet::factory()->create(['installments_count' => 2]);

        $response = $this->getJson(route('api.v1.carnet.show', $carnet->id));
        $response->assertOk();
        $response->assertJson([
            'total' => $carnet->value,
            'valor_entrada' => $carnet->down_payment,
            'parcelas' => $carnet->installments->map(function ($installment) {
                return [
                    'data_vencimento' => $installment->due_on->format('Y-m-d'),
                    'valor' => $installment->value,
                    'numero' => $installment->number,
                    'entrada' => $installment->down_payment,
                ];
            })->all(),
        ]);
    }
}
