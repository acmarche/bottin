<?php

namespace AcMarche\Bottin\Elasticsearch;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Serializer\CategorySerializer;
use AcMarche\Bottin\Serializer\FicheSerializer;
use Elastica\Document;
use Elastica\Response;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;

class ElasticIndexer
{
    use ElasticClientTrait;

    public function __construct(
        #[Autowire(env: 'MEILI_INDEX_NAME')]
        string $elasticIndexName,
        private readonly SerializerInterface $serializer,
        private readonly FicheSerializer $ficheSerializer,
        private readonly CategorySerializer $categorySerializer,
        private readonly ClassementElastic $classementElastic
    ) {
        $this->connect($elasticIndexName);
    }

    public function updateFiche(Fiche $fiche)
    {
        $data = $this->ficheSerializer->serializeFicheForElastic($fiche);
        $data['type'] = 'fiche';
        $data['classements'] = $this->classementElastic->getClassementsForApi($fiche);
        $data['cap'] = false;
        if ((is_countable($data['classements']) ? \count($data['classements']) : 0) > 0) {
            $data['cap'] = true;
        }

        $data['secteurs'] = $this->classementElastic->getSecteursForApi($data['classements']);

        $content = $this->serializer->serialize($data, 'json');
        $doc = new Document($fiche->getId(), $content);

        return $this->index->addDocument($doc);
    }

    public function updateCategorie(Category $category): Response
    {
        $data = $this->categorySerializer->serializeCategory($category);
        $data['type'] = 'category';

        $content = $this->serializer->serialize($data, 'json');
        $doc = new Document('cat_'.$data['id'], $content);

        return $this->index->addDocument($doc);
    }

    public function deleteFiche(Fiche $fiche): Response
    {
        $id = $fiche->getId();

        return $this->index->deleteById($id);
    }
}
