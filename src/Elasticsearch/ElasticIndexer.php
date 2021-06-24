<?php

namespace AcMarche\Bottin\Elasticsearch;

use AcMarche\Bottin\Elasticsearch\Data\Cleaner;
use AcMarche\Bottin\Elasticsearch\Data\DocumentElastic;
use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Serializer\CategorySerializer;
use AcMarche\Bottin\Serializer\FicheSerializer;
use Elastica\Document;
use Elastica\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ElasticIndexer
{
    use ElasticClientTrait;

    private SerializerInterface $serializer;
    private FicheSerializer $ficheSerializer;
    private CategorySerializer $categorySerializer;
    private ClassementElastic $classementElastic;

    public function __construct(
        string $elasticIndexName,
        SerializerInterface $serializer,
        FicheSerializer $ficheSerializer,
        CategorySerializer $categorySerializer,
        ClassementElastic $classementElastic
    ) {
        $this->connect($elasticIndexName);
        $this->serializer = $serializer;
        $this->ficheSerializer = $ficheSerializer;
        $this->categorySerializer = $categorySerializer;
        $this->classementElastic = $classementElastic;
    }

    public function indexFiche(Fiche $fiche): Response
    {
        $documentElastic = $this->createDocumentElastic($fiche);

        return $this->addDocument($documentElastic);
    }

    public function addDocument(DocumentElastic $documentElastic): Response
    {
        $content = $this->serializer->serialize($documentElastic, 'json');
        $id = $documentElastic->id;
        $doc = new Document($id, $content);

        return $this->index->addDocument($doc);
    }

    public function updateFiche(Fiche $fiche): Response
    {
        $data = $this->ficheSerializer->serializeFicheForElastic($fiche);
        $data['type'] = 'fiche';
        $data['classements'] = $this->classementElastic->getClassementsForApi($fiche);
        $data['cap'] = false;
        if (count($data['classements']) > 0) {
            $data['cap'] = true;
        }
        $data['secteurs'] = $this->classementElastic->getSecteursForApi($data['classements']);
        //$data['id'] = 'fiche_'.$fiche->getId();

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
        $id = 'fiche_'.$fiche->getId();

        return $this->index->deleteById($id);
    }

    private function createDocumentElastic(Fiche $fiche): DocumentElastic
    {
        $document = new DocumentElastic();
        $document->id = (string)$fiche->getId();
        $document->numero = $fiche->getNumero();
        $document->description = Cleaner::cleandata($fiche->getDescription());
        $document->expediteur = Cleaner::cleandata($fiche->getExpediteur());
        $document->categorie = $courrier->getCategorie() ? $fiche->getCategorie()->getNom() : '';
        $document->destinataires = $destinatairesId;
        $document->services = $servicesId;
        $document->original = $original; //pour affichage
        $document->copie = $copie; //pour affichage
        $document->recommande = $courrier->getRecommande();
        $document->date_courrier = $courrier->getDateCourrier()->format('Y-m-d');

        return $document;
    }

    private function updateCourrier(Fiche $fiche): Response
    {
        $documentElastic = $this->createDocumentElastic($fiche);
        $content = $this->serializer->serialize($documentElastic, 'json');
        $id = $documentElastic->id;
        $doc = new Document($id, $content);

        return $this->index->updateDocument($doc);
    }
}
