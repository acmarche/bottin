<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->renameColumn('is_by_appointment', 'is_morning_by_appointment');
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->boolean('is_noon_by_appointment')->default(false)->after('is_morning_by_appointment');
        });

        DB::table('schedules')->update([
            'is_noon_by_appointment' => DB::raw('is_morning_by_appointment'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('schedules')->update([
            'is_morning_by_appointment' => DB::raw('is_morning_by_appointment OR is_noon_by_appointment'),
        ]);

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('is_noon_by_appointment');
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->renameColumn('is_morning_by_appointment', 'is_by_appointment');
        });
    }
};
