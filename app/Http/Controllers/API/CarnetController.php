<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCarnetRequest;
use App\Http\Resources\CarnetResource;
use App\Models\Carnet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CarnetController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreCarnetRequest  $request
     *
     * @return JsonResponse
     */
    public function store(StoreCarnetRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $carnet = Carnet::create([
            'value' => $validated['valor_total'],
            'installments_count' => $validated['qtd_parcelas'],
            'first_due_date' => $validated['data_primeiro_vencimento'],
            'periodicity' => $validated['periodicidade'],
            'down_payment' => $validated['valor_entrada'] ?? 0,
        ]);

        return response()->json(new CarnetResource($carnet), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     * 
     * @return CarnetResource
     */
    public function show(Carnet $carnet): CarnetResource
    {
        return new CarnetResource($carnet);
    }
}
