<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tag_groups', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('private')->default(false);
        });

        Schema::create('tags', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->nullable()->unique();
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('private')->default(false);
            $table->foreignId('tag_group_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
        Schema::dropIfExists('tag_groups');
    }
};
