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
        Schema::table('samples', function (Blueprint $table) {
            if (!Schema::hasColumn('samples', 'type')) {
                $table->string('type')->after('name')->nullable();
            }
            if (!Schema::hasColumn('samples', 'quantity')) {
                $table->integer('quantity')->after('type')->nullable();
            }
            if (!Schema::hasColumn('samples', 'unit')) {
                $table->string('unit')->after('quantity')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            //
        });
    }
};
