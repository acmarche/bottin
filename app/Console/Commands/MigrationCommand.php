<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Media;
use App\Models\Shop;
use App\Models\Tag;
use App\Models\TagGroup;
use Illuminate\Console\Command;
use OpenAI\Laravel\Facades\OpenAI;

final class MigrationCommand extends Command
{
    private const array PHONE_COLUMNS = [
        'phone',
        'phone_other',
        'fax',
        'mobile',
        'admin_phone',
        'admin_phone_other',
        'admin_fax',
        'admin_mobile',
        'contact_phone',
        'contact_phone_other',
        'contact_fax',
        'contact_mobile',
    ];

    protected $signature = 'bottin:migration';

    protected $description = 'Find shops classified in a category that has children';

    public function handle(): int
    {
        $this->fixPhone();

        return self::SUCCESS;
    }

    private function fixPhone(): void
    {
        $shops = Shop::query()->get();
        $this->info("Processing {$shops->count()} shops...");

        $updated = 0;

        foreach ($shops as $shop) {
            $phonesToFix = [];

            foreach (self::PHONE_COLUMNS as $column) {
                $value = $shop->{$column};
                if ($value !== null && $value !== '' && ! preg_match('/^\+\d{1,3}(\s\d{2,4}){2,4}$/', $value)) {
                    $phonesToFix[$column] = $value;
                }
            }

            if ($phonesToFix === []) {
                continue;
            }

            $formatted = $this->formatPhonesWithAi($phonesToFix);

            if ($formatted === []) {
                continue;
            }

            $changes = [];
            foreach ($formatted as $column => $newValue) {
                if ($newValue !== null && $newValue !== $shop->{$column}) {
                    $changes[$column] = $newValue;
                }
            }

            if ($changes !== []) {
                $shop->updateQuietly($changes);
                $updated++;

                foreach ($changes as $column => $newValue) {
                    $this->line("  [{$shop->id}] {$column}: {$phonesToFix[$column]} → {$newValue}");
                }
            }
        }

        $this->info("Updated {$updated} shops.");
    }

    /**
     * @param  array<string, string>  $phones
     * @return array<string, string|null>
     */
    private function formatPhonesWithAi(array $phones): array
    {
        $phoneList = '';
        foreach ($phones as $column => $value) {
            $phoneList .= "{$column}: {$value}\n";
        }

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'temperature' => 0,
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                [
                    'role' => 'system',
                    'content' => <<<'PROMPT'
You are a phone number formatter. Convert Belgian phone numbers to the international format: +32 XX XX XX XX.

Rules:
- Country code is Belgium (+32) unless another country code is explicitly present.
- Remove the leading 0 from the area/mobile code when adding +32 (e.g., 084 → +32 84).
- Separate digits in groups of 2-3 with spaces: +32 84 22 44 33 or +32 475 12 34 56.
- If the input is clearly not a phone number or is unrecoverable, return null for that field.
- If a number already has a non-Belgian country code (e.g., +33, +49), keep that country code and format with spaces.
- Return ONLY a JSON object with the same keys as the input and the formatted values.
PROMPT,
                ],
                [
                    'role' => 'user',
                    'content' => $phoneList,
                ],
            ],
        ]);

        $content = $response->choices[0]->message->content;

        if ($content === null) {
            return [];
        }

        $decoded = json_decode($content, true);

        if (! is_array($decoded)) {
            return [];
        }

        return $decoded;
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

            if (! $tag) {
                $this->error("Tag '{$tagName}' not found, skipping.");

                continue;
            }

            $shops = Shop::query()
                ->where($property, true)
                ->whereDoesntHave('tags', fn ($query) => $query->where('tags.id', $tag->id))
                ->get();

            $this->info("Property '{$property}' → Tag '{$tagName}': {$shops->count()} shops to update.");

            foreach ($shops as $shop) {
                $shop->tags()->attach($tag->id);
            }
        }

        foreach ($situationToTag as $situationName => $tagName) {
            $tag = Tag::query()->where('name', $tagName)->first();

            if (! $tag) {
                $this->error("Tag '{$tagName}' not found, skipping.");

                continue;
            }

            $shops = Shop::query()
                ->whereHas('situations', fn ($query) => $query->where('name', $situationName))
                ->whereDoesntHave('tags', fn ($query) => $query->where('tags.id', $tag->id))
                ->get();

            $this->info("Situation '{$situationName}' → Tag '{$tagName}': {$shops->count()} shops to update.");

            foreach ($shops as $shop) {
                $shop->tags()->attach($tag->id);
            }
        }
    }
}
