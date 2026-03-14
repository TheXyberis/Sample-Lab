<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('action')->after('entity_id');
            $table->json('old_values')->nullable()->after('action');
            $table->json('new_values')->nullable()->after('old_values');
            $table->string('ip_address', 45)->nullable()->after('user_id');
            $table->text('user_agent')->nullable()->after('ip_address');
            
            $table->index(['entity_type', 'entity_id']);
            $table->index('action');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['entity_type', 'entity_id']);
            $table->dropIndex('action');
            $table->dropIndex('user_id');
            $table->dropIndex('created_at');
            
            $table->dropColumn(['action', 'old_values', 'new_values', 'ip_address', 'user_agent']);
        });
    }
};
