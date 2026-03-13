<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->index('status');
            $table->index(['status', 'created_at']);
        });

        Schema::table('measurements', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('result_sets', function (Blueprint $table) {
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['status', 'created_at']);
        });

        Schema::table('measurements', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('result_sets', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
