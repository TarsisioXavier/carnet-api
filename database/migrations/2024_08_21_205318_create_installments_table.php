<?php

use App\Models\Carnet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Carnet::class);
            $table->date('due_on')->comment('Due date.');
            $table->integer('number')->comment('Installment order.');
            $table->unsignedBigInteger('value')->comment('Valor da parcela.');
            $table->boolean('down_payment')->default(false)->comment('Parcela de entrada?');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
