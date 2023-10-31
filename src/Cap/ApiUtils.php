<?php

namespace AcMarche\Bottin\Cap;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Classement;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Repository\HoraireRepository;
use AcMarche\Bottin\Serializer\CategorySerializer;
use AcMarche\Bottin\Serializer\ClassementSerializer;
use AcMarche\Bottin\Serializer\FicheImageSerializer;
use AcMarche\Bottin\Serializer\FicheSerializer;
use AcMarche\Bottin\Serializer\HoraireSerializer;
use AcMarche\Bottin\Utils\PathUtils;
use JsonException;

class ApiUtils
{
    public function __construct(
        private readonly HoraireRepository $horaireRepository,
        private readonly ClassementRepository $classementRepository,
        private readonly PathUtils $pathUtils,
        public readonly CategorySerializer $categorySerializer,
        private readonly ClassementSerializer $classementSerializer,
        private readonly FicheImageSerializer $ficheImageSerializer,
        private readonly FicheSerializer $ficheSerializer,
        private readonly HoraireSerializer $horaireSerializer
    ) {
    }

    /**
     * @param Category[] $data
     */
    public function prepareCategories(array $data): array
    {
        $categories = [];

        foreach ($data as $category) {
            $cat = $this->serializeCategory($category);

            $categories[] = $cat;
        }

        return $categories;
    }

    public function serializeCategoryForAndroid(Category $category): array
    {
        $data = $this->categorySerializer->serializeCategory2($category);
        $data['path'] = $this->getSerializedPath($category);

        return $data;
    }

    public function serializeCategory(Category $category): array
    {
        $enfantsSerialized = [];
        foreach ($category->enfants as $enfant) {
            $dataEnfant = $this->categorySerializer->serializeCategory2($enfant);
            $dataEnfant['path'] = $this->getSerializedPath($enfant);
            $enfantsSerialized[] = $dataEnfant;
        }

        $data = $this->categorySerializer->serializeCategory2($category);
        $data['path'] = $this->getSerializedPath($category);
        $data['enfants'] = $enfantsSerialized;

        return $data;
    }

    private function getSerializedPath(Category $category): array
    {
        $paths = $this->pathUtils->getPath($category);
        $pathsSerialized = [];
        foreach ($paths as $path) {
            $pathsSerialized[] = $this->categorySerializer->serializePathCategoryForApi($path);
        }

        return $pathsSerialized;
    }

    public function prepareFiche(Fiche $fiche): array
    {
        $dataFiche = $this->ficheSerializer->serializeFiche($fiche);
        $dataFiche['classements'] = $this->getClassementsForApi($fiche); // only eco !!
        $dataFiche['horaires'] = $this->getHorairesForApi($fiche);
        $dataFiche['images'] = $this->getImages($fiche);
        $urls = [];
        foreach ($dataFiche['images'] as $image) {
            $urls[] = 'https://bottin.marche.be/bottin/fiches/'.$fiche->getId().'/'.$image['image_name'];
        }

        $logo = null;
        if ([] !== $urls) {
            $logo = $urls[0];
        }

        $dataFiche['logo'] = $logo;
        $dataFiche['photos'] = $urls;

        return $dataFiche;
    }

    public function prepareFicheAndroid(Fiche $fiche): array
    {
        try {
            $dataFiche = $this->ficheSerializer->serializeFiche($fiche);
            $dataFiche['horaires'] = $this->getHorairesForApi($fiche);
            $dataFiche['images'] = $this->getImages($fiche);
            $urls = [];
            foreach ($dataFiche['images'] as $image) {
                $urls[] = 'https://bottin.marche.be/bottin/fiches/'.$fiche->getId().'/'.$image['image_name'];
            }

            $dataFiche['logo'] = (is_countable($urls > 0) ? \count($urls) > 0 ? $urls[0] : null : null);
            $dataFiche['photos'] = $urls;

            return $dataFiche;
        } catch (JsonException) {
            return [];
        }
    }

    public function getImages(Fiche $fiche): array
    {
        $images = [];

        foreach ($fiche->images as $ficheImage) {
            $images[] = $this->ficheImageSerializer->serializeFicheImage($ficheImage);
        }

        return $images;
    }

    public function getHorairesForApi(Fiche $fiche): array
    {
        $data = [];
        foreach ($this->horaireRepository->findBy(['fiche' => $fiche]) as $horaire) {
            $data[] = $this->horaireSerializer->serializeHoraireForApi($horaire);
        }

        return $data;
    }

    public function getClassementsForApi(Fiche $fiche): array
    {
        $classementsFiche = $this->classementRepository->getByFiche($fiche, true);
        $classements = [];
        foreach ($classementsFiche as $classement) {
            $dataClassement = $this->classementSerializer->serializeClassementForApi($classement);
            $category = $classement->category;
            $dataClassement['path'] = $this->getPathsForApi($category);
            $classements[] = $dataClassement;
        }

        return $classements;
    }

    public function prepareClassement(Classement $classement): array
    {
        return $this->classementSerializer->serializeClassementForApiAndroid($classement);
    }

    protected function getPathsForApi(Category $category): array
    {
        $data = [];
        $paths = $this->pathUtils->getPath($category);
        foreach ($paths as $path) {
            $data[] = $this->categorySerializer->serializePathCategoryForApi($path);
        }

        return $data;
    }

    public function prepareCategoriesForAndroid(array $data): array
    {
        $categories = [];

        foreach ($data as $category) {
            $categories[] = $this->serializeCategoryForAndroid($category);
        }

        return $categories;
    }
}
