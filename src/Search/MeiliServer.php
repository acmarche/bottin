<?php

namespace AcMarche\Bottin\Search;

use AcMarche\Bottin\Elasticsearch\ClassementElastic;
use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Serializer\CategorySerializer;
use AcMarche\Bottin\Serializer\FicheSerializer;
use Meilisearch\Contracts\DeleteTasksQuery;
use Meilisearch\Endpoints\Keys;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MeiliServer
{
    use MeiliTrait;

    private string $primaryKey = 'id';
    private array $skips = [705];

    public function __construct(
        #[Autowire(env: 'BOTTIN_INDEX_NAME')]
        private string $indexName,
        #[Autowire(env: 'BOTTIN_INDEX_KEY')]
        private string $masterKey,
        private readonly FicheRepository $ficheRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly FicheSerializer $ficheSerializer,
        private readonly CategorySerializer $categorySerializer,
        private readonly ClassementElastic $classementElastic,
    ) {
    }

    /**
     *
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
     * https://raw.githubusercontent.com/meilisearch/meilisearch/latest/config.toml
     * @return array
     */
    public function settings(): array
    {
        //don't return same fiches. Suppose you have numerous black jackets in different sizes in your costumes index
        //$this->client->index($this->indexName)->updateDistinctAttribute('societe');

        /*$this->client->index($this->indexName)->updateSearchableAttributes([
            'title',
            'overview',
            'genres',
        ]);*/

        return $this->client->index($this->indexName)->updateFilterableAttributes($this->facetFields);
    }

    public function addContent(): void
    {
        $this->addFiches();
        $this->addCategories();
    }


    /**
     * @return void
     */
    public function addFiches(): void
    {
        $this->init();
        $documents = [];
        foreach ($this->ficheRepository->findAllWithJoins() as $fiche) {
            $documents[] = $this->updateFiche($fiche);
        }
        $index = $this->client->index($this->indexName);
        $index->addDocuments($documents, $this->primaryKey);
    }

    private function updateFiche(Fiche $fiche): array
    {
        $data = $this->ficheSerializer->serializeFicheForElastic($fiche);
        $data['type'] = 'fiche';
        $data['classements'] = $this->classementElastic->getClassementsForApi($fiche);
        $data['cap'] = false;
        if ((is_countable($data['classements']) ? \count($data['classements']) : 0) > 0) {
            $data['cap'] = true;
        }
        if ($fiche->latitude && $fiche->longitude) {
            $data['_geo'] = ['lat' => $fiche->latitude, 'lng' => $fiche->longitude];
        }

        $data['secteurs'] = $this->classementElastic->getSecteursForApi($data['classements']);

        return $data;
    }

    public function addCategories(): void
    {
        $documents = [];
        foreach ($this->categoryRepository->findAll() as $category) {
            if (\in_array($category->getId(), $this->skips, true)) {
                continue;
            }

            $documents[] = $this->updateCategory($category);
        }

        $index = $this->client->index($this->indexName);
        $index->addDocuments($documents, $this->primaryKey);
    }

    /**
     * @throws \JsonException
     */
    private function updateCategory(Category $category): array
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
}