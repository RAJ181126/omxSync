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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('task_performance_total', 5, 2)->default(0.00);
            $table->unsignedTinyInteger('attendance_points')->default(0);
            $table->decimal('final_score', 5, 2)->default(0.00);
            $table->string('grade', 2)->nullable();
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();
            //indexes columns
            $table->index('user_id');
            $table->index('grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
