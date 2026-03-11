<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->boolean('is_main')->default(false);
            $table->string('file_name');
            $table->string('mime_type');
            $table->string('size')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
