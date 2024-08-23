<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstallmentResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'data_vencimento' => $this->due_on->format('Y-m-d'),
            'valor' => round($this->value, 2),
            'numero' => $this->number,
            'entrada' => $this->down_payment,
        ];
    }
}
