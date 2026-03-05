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
        Schema::create('samples', function (Blueprint $table) {

        $table->id();

        $table->string('sample_code')->unique();

        $table->foreignId('client_id')->constrained();
        $table->foreignId('project_id')->nullable()->constrained();

        $table->string('name');

        $table->string('status')->default('REGISTERED');

        $table->foreignId('location_id')->nullable();

        $table->decimal('quantity',10,2)->nullable();
        $table->string('unit')->nullable();

        $table->timestamp('collected_at')->nullable();
        $table->timestamp('received_at')->nullable();
        $table->timestamp('expires_at')->nullable();

        $table->json('metadata_json')->nullable();

        $table->string('barcode_value')->nullable();
        $table->string('qr_value')->nullable();

        $table->foreignId('created_by');

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('samples');
    }
};
