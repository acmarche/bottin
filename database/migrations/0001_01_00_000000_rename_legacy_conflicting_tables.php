<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Renames legacy tables that conflict with the new Laravel schema.
 * Must run BEFORE the new schema creation migrations.
 */
return new class() extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('meta_data');
        Schema::dropIfExists('meta_field');
        Schema::dropIfExists('demande_metas');
        Schema::dropIfExists('demande');
        Schema::dropIfExists('messenger_messages');
    }
};
