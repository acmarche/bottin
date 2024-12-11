<?php

namespace AcMarche\Bottin\History;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\FicheImage;
use AcMarche\Bottin\Entity\History;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Repository\HistoryRepository;
use AcMarche\Bottin\Utils\PathUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\ByteString;

class HistoryUtils
{
    private array $skip = ['createdAt', 'updatedAt', 'id'];

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly FicheRepository $ficheRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly HistoryRepository $historyRepository,
        private readonly PathUtils $pathUtils,
    ) {}

    public function diffFicheNew(Fiche $fiche, array $changes): void
    {
        $username = $this->getUsername();
        foreach ($changes as $propertyName => $values) {
            if (!in_array($propertyName, $this->skip)) {
                $this->createForFiche($fiche, $username, $propertyName, $values[0], $values[1]);
            }
        }
    }

    public function diffFiche(Fiche $fiche): void
    {
        $username = $this->getUsername();

        $originalData = $this->ficheRepository->getOriginalEntityData($fiche);
        $toArrayEntity = $this->ficheToArray($fiche);
        unset($toArrayEntity['created_at']);
        unset($toArrayEntity['updated_at']);
        unset($toArrayEntity['id']);

        $changes = array_diff_assoc($toArrayEntity, $originalData);
        foreach ($changes as $property => $change) {
            $this->createForFiche($fiche, $username, $property, $originalData[$property], $change);
        }

        if ([] !== $changes) {
            $this->entityManager->flush();
        }
    }

    private function ficheToArray(Fiche $fiche): array
    {
        $data = $this->serializer->serialize($fiche, 'json', ['groups' => 'group1']);

        try {
            return json_decode($data, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return [];
        }
    }

    private function getUsername(): string
    {
        $username = null;
        if (($user = $this->security->getUser()) instanceof UserInterface) {
            $username = $user->getUserIdentifier();
        }

        if (null === $username) {
            $username = 'token';
        }

        return $username;
    }

    private function createForFiche(
        ?Fiche $fiche,
        ?string $made_by,
        ?string $property,
        ?string $oldValue,
        ?string $newValue,
    ): void {
        $oldValue = $this->convertUtf8($oldValue);
        $newValue = $this->convertUtf8($newValue);
        $history = new History($fiche, $made_by, $property, $oldValue, $newValue);
        $this->historyRepository->persist($history);
    }

    public function diffClassement(Fiche $fiche, Category $category, string $action): void
    {
        $username = $this->getUsername();
        $path = $this->pathUtils->getPath($category);
        $classementPath = implode(' > ', $path);
        $this->createForFiche($fiche, $username, 'classement', $action, $classementPath);
        $this->flush();
    }

    public function newFiche(Fiche $fiche): void
    {
        $username = $this->getUsername();
        $this->createForFiche($fiche, $username, 'nouvelle fiche', '', '');
        $this->flush();
    }

    public function deleteFiche(string $nomFiche): void
    {
        $username = $this->getUsername();
        $this->createForFiche(null, $username, 'suppression de fiche', $nomFiche, '');
        $this->flush();
    }

    public function addImage(Fiche $fiche, FicheImage $ficheImage): void
    {
        $username = $this->getUsername();
        $this->createForFiche($fiche, $username, 'ajout image', '', $ficheImage->imageName);
        $this->flush();
    }

    private function convertUtf8(?string $text): ?string
    {
        if ($text) {
            if (strlen($text) > 255) {
                $text = substr($text, 0, 250).'...';
            }
            $textObject = (new ByteString($text));
            if (!$textObject->isUtf8()) {
                $text = iconv("UTF-8", "UTF-8//IGNORE", $text);
            }
        }

        return $text;
    }

    private function flush(): void
    {
        try {
            $this->historyRepository->flush();
        } catch (\Exception $exception) {
        }
    }
}
