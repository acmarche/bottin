<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops');
            $table->integer('day')->nullable();
            $table->string('media_path')->nullable();
            $table->boolean('is_open_at_lunch')->default(false);
            $table->boolean('is_by_appointment')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->time('morning_start')->nullable();
            $table->time('morning_end')->nullable();
            $table->time('noon_start')->nullable();
            $table->time('noon_end')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
