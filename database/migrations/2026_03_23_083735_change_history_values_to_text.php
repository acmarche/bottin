<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('histories', function (Blueprint $table): void {
            $table->text('old_value')->nullable()->change();
            $table->text('new_value')->nullable()->change();
        });
    }
};
