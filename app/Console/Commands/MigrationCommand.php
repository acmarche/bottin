<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Media;
use App\Models\Shop;
use App\Models\Tag;
use App\Models\TagGroup;
use Illuminate\Console\Command;

final class MigrationCommand extends Command
{
    protected $signature = 'bottin:migration';

    protected $description = 'Find shops classified in a category that has children';

    public function handle(): int
    {
        $this->fixPath();
        $this->checkTags();
        Shop::query()->update(['enabled' => true]);

        return self::SUCCESS;
    }

    private function fixPath(): void
    {
        $medias = Media::query()->get();

        $this->warn("Found {$medias->count()} medias");
        $this->newLine();

        foreach ($medias as $media) {
            $shop = $media->shop;
            if ($shop) {
                $media->file_name = Media::BASE_PATH.$shop->id.'/'.$media->file_name;
                $media->save();
            }
        }
    }

    private function checkTags(): void
    {
        $groupService = TagGroup::create(['name' => 'Services']);
        Tag::create([
            'name' => 'Ouvert le midi',
            'tag_group_id' => $groupService->id,
        ]);
        Tag::create(['name' => 'Click & Collect', 'tag_group_id' => $groupService->id]);

        $groupPae = TagGroup::create(['name' => 'PAE']);
        Tag::query()
            ->where('name', 'like', 'PAE %')
            ->update(['tag_group_id' => $groupPae->id]);

        $this->info("Moved PAE tags to group '{$groupPae->name}'.");

        $situationToTag = [
            'Boulevard' => 'Boulevard urbain',
            'Centre ville' => 'Centre ville',
            'Village' => 'Village',
            'Zoning' => 'Zoning',
        ];

        $propertiesToTag = [
            'city_center' => 'Centre ville',
            'open_at_lunch' => 'Ouvert le midi',
            'pmr' => 'Pmr',
            'click_collect' => 'Click & Collect',
            'ecommerce' => 'Ecommerce',
        ];

        foreach ($propertiesToTag as $property => $tagName) {
            $tag = Tag::query()->where('name', $tagName)->first();

            if (!$tag) {
                $this->error("Tag '{$tagName}' not found, skipping.");

                continue;
            }

            $shops = Shop::query()
                ->where($property, true)
                ->whereDoesntHave('tags', fn($query) => $query->where('tags.id', $tag->id))
                ->get();

            $this->info("Property '{$property}' → Tag '{$tagName}': {$shops->count()} shops to update.");

            foreach ($shops as $shop) {
                $shop->tags()->attach($tag->id);
            }
        }

        foreach ($situationToTag as $situationName => $tagName) {
            $tag = Tag::query()->where('name', $tagName)->first();

            if (!$tag) {
                $this->error("Tag '{$tagName}' not found, skipping.");

                continue;
            }

            $shops = Shop::query()
                ->whereHas('situations', fn($query) => $query->where('name', $situationName))
                ->whereDoesntHave('tags', fn($query) => $query->where('tags.id', $tag->id))
                ->get();

            $this->info("Situation '{$situationName}' → Tag '{$tagName}': {$shops->count()} shops to update.");

            foreach ($shops as $shop) {
                $shop->tags()->attach($tag->id);
            }
        }
    }
}
