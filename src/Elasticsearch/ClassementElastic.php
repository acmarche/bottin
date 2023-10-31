<?php

namespace AcMarche\Bottin\Elasticsearch;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Serializer\ClassementSerializer;

class ClassementElastic
{
    public function __construct(
        private readonly ClassementRepository $classementRepository,
        private readonly ClassementSerializer $classementSerializer
    ) {
    }

    /**
     * Pour cap.
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
