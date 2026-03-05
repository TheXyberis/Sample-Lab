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
        Schema::create('methods', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('base_method_id')->nullable();

            $table->string('name');

            $table->integer('version')->default(1);

            $table->enum('status', ['DRAFT','PUBLISHED'])->default('DRAFT');

            $table->json('schema_json')->nullable();
            $table->json('limits_json')->nullable();

            $table->foreignId('created_by')->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('methods');
    }
};
