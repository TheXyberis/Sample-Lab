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
        Schema::create('measurements', function (Blueprint $table){
            $table->id();
            $table->foreignId('sample_id')->constrained('samples');
            $table->foreignId('method_id')->constrained('methods');
            $table->enum('status',['PLANNED','RUNNING','DONE','CANCELLED','REPEAT_REQUIRED'])->default('PLANNED');
            $table->foreignId('assignee_id')->nullable()->constrained('users');
            $table->integer('priority')->default(1);
            $table->timestamp('planned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurements');
    }
};
