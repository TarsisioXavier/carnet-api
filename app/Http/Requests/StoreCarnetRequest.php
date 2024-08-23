<?php

namespace App\Http\Requests;

use App\Models\Types\CarnetPeriodicity;
use App\Rules\ValidCarnetPeriodicity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCarnetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'valor_total' => 'required|numeric',
            'qtd_parcelas' => 'required|numeric|integer',
            'data_primeiro_vencimento' => 'required|date:Y-m-d',
            'periodicidade' => [
                'required',
                'string',
                new ValidCarnetPeriodicity(),
            ],
            'valor_entrada' => 'sometimes|numeric|lte:valor_total',
        ];
    }
}
