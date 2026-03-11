<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_situation', function (Blueprint $table): void {
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->foreignId('situation_id')->constrained('situations')->cascadeOnDelete();
            $table->primary(['shop_id', 'situation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_situation');
    }
};
