<?php

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
        Schema::create('carnets', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('value')->comment('O valor total do carnê.');
            $table->integer('installments_count')->comment('A quantidade de parcelas.');
            $table->date('first_due_date')->comment('A data do primeiro vencimento.');
            $table->string('periodicity')->comment('A periodicidade das parcelas.');
            $table->decimal('down_payment', 20, 2)->default(0)->comment('O valor da entrada.');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carnets');
    }
};
