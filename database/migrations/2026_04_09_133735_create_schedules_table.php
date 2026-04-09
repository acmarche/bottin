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
        Schema::create('schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('shop_id');
            $table->integer('day')->nullable();
            $table->string('media_path')->nullable();
            $table->boolean('is_by_appointment')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->time('morning_start')->nullable();
            $table->time('morning_end')->nullable();
            $table->time('noon_start')->nullable();
            $table->time('noon_end')->nullable();

            $table->unique(['shop_id', 'day']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
