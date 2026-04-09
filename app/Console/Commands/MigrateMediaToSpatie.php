<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Shop;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use function basename;

final class MigrateMediaToSpatie extends Command
{
    protected $signature = 'bottin:migrate-media {--dry-run : Show what would be migrated without writing}';

    protected $description = 'Migrate media from mediaold table to Spatie media table';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $rows = DB::table('mediaold')->orderBy('id')->get();

        $this->info("Found {$rows->count()} media records to migrate.");

        if ($dryRun) {
            $this->warn('Dry run mode — no data will be written.');
        }

        $migrated = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $shopExists = DB::table('shops')->where('id', $row->shop_id)->exists();

            if (!$shopExists) {
                $this->warn("Skipping media #{$row->id}: shop #{$row->shop_id} does not exist.");
                $skipped++;

                continue;
            }

            $fileName = basename($row->file_name);
            if ($row->name) {
                $name = $row->name;
            } else {
                $name = $fileName;
            }

            if (!$dryRun) {
                $fileSize = $row->size;

                if (!$fileSize && Storage::disk('public')->exists($row->file_name)) {
                    $fileSize = Storage::disk('public')->size($row->file_name);
                }

                DB::table('media')->insert([
                    'model_type' => Shop::class,
                    'model_id' => $row->shop_id,
                    'collection_name' => 'images',
                    'name' => $name,
                    'file_name' => $fileName,
                    'mime_type' => $row->mime_type,
                    'disk' => 'public',
                    'conversions_disk' => 'public',
                    'size' => (int)($fileSize ?? 0),
                    'manipulations' => '[]',
                    'custom_properties' => json_encode(['is_main' => (bool)$row->is_main]),
                    'generated_conversions' => '[]',
                    'responsive_images' => '[]',
                    'order_column' => $row->id,
                    'created_at' => $row->updated_at ?? now(),
                    'updated_at' => $row->updated_at ?? now(),
                ]);
            }

            $migrated++;
        }

        $this->newLine();
        $this->info("Migrated: {$migrated}");
        $this->info("Skipped: {$skipped}");

        if ($dryRun) {
            $this->warn('Re-run without --dry-run to perform the migration.');
        }

        return self::SUCCESS;
    }
}
