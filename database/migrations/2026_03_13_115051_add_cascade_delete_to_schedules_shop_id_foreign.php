<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table): void {
            $table->dropForeign(['shop_id']);
            $table->foreignId('shop_id')->change()->constrained('shops')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table): void {
            $table->dropForeign(['shop_id']);
            $table->foreignId('shop_id')->change()->constrained('shops');
        });
    }
};
