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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('result_set_id')->constrained()->cascadeOnDelete();
            $table->string('field_key');
            $table->text('value_text')->nullable();
            $table->double('value_num')->nullable();
            $table->string('unit')->nullable();
            $table->json('flags_json')->nullable(); // ["OUT_OF_RANGE":true, "SUSPECT":true]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
