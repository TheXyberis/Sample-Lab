<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('result_sets', function (Blueprint $table) {
            $table->string('rejected_by')->nullable()->after('locked_at');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            $table->text('rejection_reason')->nullable()->after('rejected_at');
        });
    }

    public function down()
    {
        Schema::table('result_sets', function (Blueprint $table) {
            $table->dropColumn(['rejected_by', 'rejected_at', 'rejection_reason']);
        });
    }
};
