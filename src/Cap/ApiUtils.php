<?php


namespace AcMarche\Bottin\Cap;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Repository\HoraireRepository;
use AcMarche\Bottin\Serializer\CategorySerializer;
use AcMarche\Bottin\Serializer\ClassementSerializer;
use AcMarche\Bottin\Serializer\FicheImageSerializer;
use AcMarche\Bottin\Serializer\FicheSerializer;
use AcMarche\Bottin\Serializer\HoraireSerializer;
use AcMarche\Bottin\Service\CategoryService;
use AcMarche\Bottin\Utils\PathUtils;

class ApiUtils
{
    /**
     * @var CategoryService
     */
    private $categoryService;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var FicheRepository
     */
    private $ficheRepository;
    /**
     * @var PathUtils
     */
    private $pathUtils;
    /**
     * @var ClassementRepository
     */
    private $classementRepository;
    /**
     * @var HoraireRepository
     */
    private $horaireRepository;
    /**
     * @var CategorySerializer
     */
    private $categorySerializer;
    /**
     * @var ClassementSerializer
     */
    private $classementSerializer;
    /**
     * @var FicheImageSerializer
     */
    private $ficheImageSerializer;
    /**
     * @var FicheSerializer
     */
    private $ficheSerializer;
    /**
     * @var HoraireSerializer
     */
    private $horaireSerializer;

    public function __construct(
        CategoryService $categoryService,
        CategoryRepository $categoryRepository,
        FicheRepository $ficheRepository,
        HoraireRepository $horaireRepository,
        ClassementRepository $classementRepository,
        PathUtils $pathUtils,
        CategorySerializer $categorySerializer,
        ClassementSerializer $classementSerializer,
        FicheImageSerializer $ficheImageSerializer,
        FicheSerializer $ficheSerializer,
        HoraireSerializer $horaireSerializer
    ) {
        $this->categoryService = $categoryService;
        $this->categoryRepository = $categoryRepository;
        $this->ficheRepository = $ficheRepository;
        $this->pathUtils = $pathUtils;
        $this->classementRepository = $classementRepository;
        $this->horaireRepository = $horaireRepository;
        $this->categorySerializer = $categorySerializer;
        $this->classementSerializer = $classementSerializer;
        $this->ficheImageSerializer = $ficheImageSerializer;
        $this->ficheSerializer = $ficheSerializer;
        $this->horaireSerializer = $horaireSerializer;
    }

    /**
     * @param Category[] $data
     * @return array
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

    public function serializeCategory(Category $category)
    {
        $enfantsSerialized = [];
        foreach ($category->getEnfants() as $enfant) {
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
        $dataFiche['classements'] = $this->getClassementsForApi($fiche);//only eco !!
        $dataFiche['horaires'] = $this->getHorairesForApi($fiche);
        $dataFiche['images'] = $this->getImages($fiche);
        $dataFiche['photos'] = [];

        return $dataFiche;
    }

    protected function getImages(Fiche $fiche): array
    {
        $images = [];

        foreach ($fiche->getImages() as $ficheImage) {
            $images[] = $this->ficheImageSerializer->serializeFicheImage($ficheImage);
        }

        return $images;
    }

    protected function getHorairesForApi(Fiche $fiche)
    {
        $data = [];
        foreach ($this->horaireRepository->findBy(['fiche' => $fiche]) as $horaire) {
            $data[] = $this->horaireSerializer->serializeHoraireForApi($horaire);
        }

        return $data;
    }

    public function getClassementsForApi(Fiche $fiche)
    {
        $classementsFiche = $this->classementRepository->getByFiche($fiche,true);
        $classements = [];
        foreach ($classementsFiche as $classement) {
            $dataClassement = $this->classementSerializer->serializeClassementForApi($classement);
            $category = $classement->getCategory();
            $dataClassement['path'] = $this->getPathsForApi($category);
            $classements[] = $dataClassement;
        }

        return $classements;
    }


    protected function getPathsForApi(Category $category)
    {
        $data = [];
        $paths = $this->pathUtils->getPath($category);
        foreach ($paths as $path) {
            $data[] = $this->categorySerializer->serializePathCategoryForApi($path);
        }

        return $data;
    }

}
