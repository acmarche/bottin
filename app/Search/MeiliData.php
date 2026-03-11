<?php

declare(strict_types=1);

namespace App\Search;

use App\Models\Category;
use App\Models\Shop;
use App\Services\Bottin;

use function in_array;

final class MeiliData
{
    use MeiliTrait;

    public function __construct()
    {
        $this->indexName = config('bottin.meili.index_name');
        $this->masterKey = config('bottin.meili.key');
    }

    public function addFiches(): void
    {
        $this->initClientAndIndex();
        $documents = [];
        foreach (Shop::with(['tags.tagGroup', 'tags', 'categories', 'medias'])->get() as $fiche) {
            $documents[] = $fiche->toSearchableArray();
        }
        $index = $this->client->index($this->indexName);
        $index->addDocuments($documents, $this->primaryKey);
    }

    public function updateFiche(Shop $fiche): void
    {
        $this->initClientAndIndex();
        $fiche->loadMissing(['tags.tagGroup', 'categories', 'medias']);
        $documents = [$fiche->toSearchableArray()];
        $index = $this->client->index($this->indexName);
        $index->addDocuments($documents, $this->primaryKey);
    }

    public function removeFiche(int $ficheId): void
    {
        $this->initClientAndIndex();
        $index = $this->client->index($this->indexName);
        $index->deleteDocument($ficheId);
    }

    public function addCategories(): void
    {
        $this->initClientAndIndex();
        $documents = [];
        foreach (Category::with('parent')->get() as $category) {
            if (in_array($category->id, Bottin::SEARCH_SKIP, true)) {
                continue;
            }

            $documents[] = $this->createDocumentCategory($category);
        }

        $index = $this->client->index($this->indexName);
        $index->addDocuments($documents, $this->primaryKey);
    }

    private function createDocumentCategory(Category $category): array
    {
        return [
            'id' => 'cat_'.$category->id,
            'name' => $category->name,
            'description' => $category->description,
            'logo' => $category->logo ?? '',
            'icon' => $category->icon,
            'slug' => $category->slug,
            'parent_id' => $category->parent_id,
            'type' => 'category',
        ];
    }
}
