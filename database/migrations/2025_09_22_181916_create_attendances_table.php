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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->boolean('punctual')->default(false);
            $table->integer('minutes_late')->nullable();
            $table->timestamps();
            // unique user entry per date
            $table->unique(['user_id', 'date']);
            // index columns
            $table->index(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
