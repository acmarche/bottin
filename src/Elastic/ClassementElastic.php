<?php


namespace AcMarche\Bottin\Elastic;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Serializer\ClassementSerializer;

class ClassementElastic
{
    private \AcMarche\Bottin\Repository\ClassementRepository $classementRepository;
    private \AcMarche\Bottin\Serializer\ClassementSerializer $classementSerializer;

    public function __construct(ClassementRepository $classementRepository, ClassementSerializer $classementSerializer)
    {
        $this->classementRepository = $classementRepository;
        $this->classementSerializer = $classementSerializer;
    }

    /**
     * Pour cap
     * @param Fiche $fiche
     */
    public function getClassementsForApi(Fiche $fiche): array
    {
        $classementsFiche = $this->classementRepository->getByFiche($fiche, true);
        $classements = [];
        foreach ($classementsFiche as $classement) {
            $classements[] = $this->classementSerializer->serializeClassementForApi($classement);
        }

        return $classements;
    }

    public function getSecteursForApi(array $classements): array
    {
        $tags = [];
        foreach ($classements as $classement) {
            $tags[] = $classement['name'];
        }

        return $tags;
    }
}
