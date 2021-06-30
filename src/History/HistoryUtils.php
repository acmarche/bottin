<?php

namespace AcMarche\Bottin\History;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\History;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Repository\HistoryRepository;
use AcMarche\Bottin\Utils\PathUtils;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class HistoryUtils
{
    private SerializerInterface $serializer;
    private FicheRepository $ficheRepository;
    private Security $security;
    private HistoryRepository $historyRepository;
    private PathUtils $pathUtils;

    public function __construct(
        SerializerInterface $serializer,
        FicheRepository $ficheRepository,
        Security $security,
        HistoryRepository $historyRepository,
        PathUtils $pathUtils
    ) {
        $this->serializer = $serializer;
        $this->ficheRepository = $ficheRepository;
        $this->security = $security;
        $this->historyRepository = $historyRepository;
        $this->pathUtils = $pathUtils;
    }

    public function diffFiche(Fiche $fiche)
    {
        $username = $this->getUsername();

        $originalData = $this->ficheRepository->getOriginalEntityData($fiche);
        $toArrayEntity = $this->ficheToArray($fiche);
        unset($toArrayEntity['created_at']);
        unset($toArrayEntity['updated_at']);
        $changes = array_diff_assoc($toArrayEntity, $originalData);
        foreach ($changes as $property => $change) {
            $this->createForFiche($fiche, $username, $property, $originalData[$property], $change);
        }
        if (count($changes) > 0) {
            $this->historyRepository->flush();
        }
    }

    private function ficheToArray(Fiche $fiche): array
    {
        $data = $this->serializer->serialize($fiche, 'json', ['groups' => 'group1']);
        $data = json_decode($data, true);

        return $data;
    }

    private function getUsername(): ?string
    {
        $username = null;
        if ($user = $this->security->getUser()) {
            $username = $user->getUserIdentifier();
        }

        return $username;
    }

    private function createForFiche(
        ?Fiche $fiche,
        ?string $made_by,
        ?string $property,
        ?string $oldValue,
        ?string $newValue
    ) {
        $history = new History($fiche, $made_by, $property, $oldValue, $newValue);
        $this->historyRepository->persist($history);
    }

    public function diffClassement(Fiche $fiche, Category $category)
    {
        $username = $this->getUsername();
        $path = $this->pathUtils->getPath($category);
        $classementPath = join(' > ', $path);
        $this->createForFiche($fiche, $username, 'classement', null, $classementPath);
    }
}
