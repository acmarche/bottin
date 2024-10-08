<?php

namespace AcMarche\Bottin\Search;

use AcMarche\Bottin\Bottin;
use AcMarche\Bottin\Cap\CapApi;
use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Serializer\CategorySerializer;
use AcMarche\Bottin\Serializer\FicheSerializer;
use AcMarche\Cap\Entity\Commercant;
use AcMarche\Cap\Repository\CommercantRepository;
use AcMarche\Cap\Repository\CommercioBottinRepository;
use AcMarche\Cap\Repository\GalleryRepository;
use Meilisearch\Contracts\DeleteTasksQuery;
use Meilisearch\Endpoints\Keys;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MeiliServer
{
    use MeiliTrait;

    private string $primaryKey = 'id';

    public function __construct(
        #[Autowire(env: 'MEILI_INDEX_NAME')]
        private string $indexName,
        #[Autowire(env: 'MEILI_MASTER_KEY')]
        private string $masterKey,
        private readonly FicheRepository $ficheRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly FicheSerializer $ficheSerializer,
        private readonly CategorySerializer $categorySerializer,
        private readonly ClassementElastic $classementElastic,
        private readonly CommercioBottinRepository $commercioBottinRepository,
        private readonly CommercantRepository $commercantRepository,
        private readonly GalleryRepository $galleryRepository,
        private readonly CapApi $capApi
    ) {
    }

    /**
     * @return array<'taskUid','indexUid','status','enqueuedAt'>
     */
    public function createIndex(): array
    {
        $this->init();
        $this->client->deleteTasks((new DeleteTasksQuery())->setStatuses(['failed', 'canceled', 'succeeded']));
        $this->client->deleteIndex($this->indexName);

        return $this->client->createIndex($this->indexName, ['primaryKey' => $this->primaryKey]);
    }

    /**
     * https://raw.githubusercontent.com/meilisearch/meilisearch/latest/config.toml.
     */
    public function settings(): array
    {
        // don't return same fiches. Suppose you have numerous black jackets in different sizes in your costumes index
        // $this->client->index($this->indexName)->updateDistinctAttribute('societe');

        /*$this->client->index($this->indexName)->updateSearchableAttributes([
            'title',
            'overview',
            'genres',
        ]);*/

        return $this->client->index($this->indexName)->updateFilterableAttributes($this->facetFields);
    }

    /**
     * https://github.com/yooper/stop-words/blob/master/data/stop-words_french_1_fr.txt.
     */
    public function stopWords(): void
    {
        $this->client->index($this->indexName)->updateStopWords(['the', 'of', 'to']);
    }

    public function addContent(): void
    {
        $this->addFiches();
        $this->addCategories();
    }

    public function addFiches(): void
    {
        $this->init();
        $documents = [];
        foreach ($this->ficheRepository->findAllWithJoins() as $fiche) {
            $documents[] = $this->createDocumentFiche($fiche, true);
        }
        $index = $this->client->index($this->indexName);
        $index->addDocuments($documents, $this->primaryKey);
    }

    private function createDocumentFiche(Fiche $fiche, bool $addCap = false): array
    {
        $data = $this->ficheSerializer->serializeFicheForElastic($fiche);
        $data['type'] = 'fiche';
        $data['classements'] = $this->classementElastic->getClassementsForApi($fiche);
        $data['capMember'] = false;
        if ((is_countable($data['classements']) ? \count($data['classements']) : 0) > 0) {
            $data['capMember'] = true;
        }
        if ($fiche->latitude && $fiche->longitude) {
            $data['_geo'] = ['lat' => $fiche->latitude, 'lng' => $fiche->longitude];
        }
        $data['secteurs'] = $this->classementElastic->getSecteursForApi($data['classements']);
        if ($addCap) {
            $data['cap'] = $this->addCapInfo($data);
        }

        return $data;
    }

    public function updateFiche(Fiche $fiche): void
    {
        $this->init();
        $documents = [$this->createDocumentFiche($fiche)];
        $index = $this->client->index($this->indexName);
        $index->addDocuments($documents, $this->primaryKey);
    }

    public function removeFiche(int $ficheId): void
    {
        $this->init();
        $index = $this->client->index($this->indexName);
        $index->deleteDocument($ficheId);
    }

    public function addCategories(): void
    {
        $documents = [];
        foreach ($this->categoryRepository->findAll() as $category) {
            if (\in_array($category->getId(), Bottin::SEARCH_SKIP, true)) {
                continue;
            }

            $documents[] = $this->createDocumentCategory($category);
        }

        $index = $this->client->index($this->indexName);
        $index->addDocuments($documents, $this->primaryKey);
    }

    /**
     * @throws \JsonException
     */
    private function createDocumentCategory(Category $category): array
    {
        $data = $this->categorySerializer->serializeCategory($category);
        $data['type'] = 'category';
        $data['id'] = 'cat_'.$data['id'];

        return $data;
    }

    public function createKey(): Keys
    {
        $this->init();

        return $this->client->createKey([
            'description' => 'Bottin API key',
            'actions' => ['*'],
            'indexes' => [$this->indexName],
            'expiresAt' => '2042-04-02T00:42:42Z',
        ]);
    }

    private function addCapInfo(array $data): ?Commercant
    {
        $capFiche = null;
        if ($cap = $this->commercioBottinRepository->findByFicheId($data['id'])) {
            if ($cap->commercantId) {
                if ($capFiche = $this->commercantRepository->findByIdCommercant($cap->commercantId)) {
                    $galleries = $this->galleryRepository->findByCommercant($capFiche);
                    $images = [];
                    foreach ($galleries as $gallery) {
                        $img = [
                            'id' => $gallery->getId(),
                            'name' => $gallery->name,
                            'path' => $gallery->mediaPath,
                            'alt' => $gallery->alt,
                        ];
                        $images[] = $img;
                    }
                    $capFiche->images = $images;
                    if ([] !== $images) {
                        if (!$capFiche->profileMediaPath) {
                            $capFiche->profileMediaPath = $images[0]['path'];
                        }
                    } else {
                        $capFiche->profileMediaPath = null;
                    }
                }
            }
        }

        return $capFiche;
    }
}
