<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->index(['client_id', 'status']);
            $table->index(['project_id', 'status']);
            $table->index(['sample_code']);
            $table->index(['created_at']);
            $table->index(['status']);
        });

        Schema::table('measurements', function (Blueprint $table) {
            $table->index(['sample_id', 'status']);
            $table->index(['method_id']);
            $table->index(['assignee_id', 'status']);
            $table->index(['planned_at']);
            $table->index(['status']);
        });

        Schema::table('result_sets', function (Blueprint $table) {
            $table->index(['measurement_id', 'status']);
            $table->index(['submitted_by', 'status']);
            $table->index(['reviewed_by', 'status']);
            $table->index(['approved_by', 'status']);
            $table->index(['submitted_at']);
            $table->index(['status']);
        });

        Schema::table('results', function (Blueprint $table) {
            $table->index(['result_set_id', 'field_key']);
            $table->index(['result_set_id']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index(['entity_type', 'entity_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });

        Schema::table('methods', function (Blueprint $table) {
            $table->index(['status']);
            $table->index(['created_by']);
            $table->index(['name']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['email']);
            $table->index(['created_at']);
        });
    }

    public function down()
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->dropIndex(['client_id', 'status']);
            $table->dropIndex(['project_id', 'status']);
            $table->dropIndex(['sample_code']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status']);
        });

        Schema::table('measurements', function (Blueprint $table) {
            $table->dropIndex(['sample_id', 'status']);
            $table->dropIndex(['method_id']);
            $table->dropIndex(['assignee_id', 'status']);
            $table->dropIndex(['planned_at']);
            $table->dropIndex(['status']);
        });

        Schema::table('result_sets', function (Blueprint $table) {
            $table->dropIndex(['measurement_id', 'status']);
            $table->dropIndex(['submitted_by', 'status']);
            $table->dropIndex(['reviewed_by', 'status']);
            $table->dropIndex(['approved_by', 'status']);
            $table->dropIndex(['submitted_at']);
            $table->dropIndex(['status']);
        });

        Schema::table('results', function (Blueprint $table) {
            $table->dropIndex(['result_set_id', 'field_key']);
            $table->dropIndex(['result_set_id']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['entity_type', 'entity_id', 'created_at']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['action', 'created_at']);
        });

        Schema::table('methods', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['name']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['created_at']);
        });
    }
};
