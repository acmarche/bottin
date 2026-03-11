<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class() extends Migration
{
    public function up(): void
    {
        $this->generateSlugs('shops', 'company');
        $this->generateSlugs('categories', 'name');
        $this->generateSlugs('addresses', 'name');
        $this->generateSlugs('tags', 'name');

        Schema::table('shops', function (Blueprint $table): void {
            $table->unique('slug');
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->unique('slug');
        });

        Schema::table('addresses', function (Blueprint $table): void {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
        });

        Schema::table('addresses', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
        });
    }

    private function generateSlugs(string $table, string $sourceField): void
    {
        $records = DB::table($table)
            ->whereNull('slug')
            ->orWhere('slug', '')
            ->get(['id', $sourceField]);

        foreach ($records as $record) {
            $slug = Str::slug($record->{$sourceField} ?? '');

            if ($slug === '') {
                $slug = 'n-a';
            }

            $originalSlug = $slug;
            $counter = 1;

            while (DB::table($table)->where('slug', $slug)->where('id', '!=', $record->id)->exists()) {
                $slug = $originalSlug.'-'.$counter;
                $counter++;
            }

            DB::table($table)->where('id', $record->id)->update(['slug' => $slug]);
        }
    }
};
