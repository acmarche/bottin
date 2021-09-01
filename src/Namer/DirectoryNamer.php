<?php

namespace AcMarche\Bottin\Namer;

use AcMarche\Bottin\Entity\Classement;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class DirectoryNamer implements DirectoryNamerInterface
{
    protected function getExtension(UploadedFile $uploadedFile): ?string
    {
        $originalName = $uploadedFile->getClientOriginalName();
        if (($extension = pathinfo($originalName, PATHINFO_EXTENSION)) !== '') {
            return $extension;
        }
        if ($extension = $uploadedFile->guessExtension()) {
            return $extension;
        }

        return null;
    }

    /**
     * Creates a directory name for the file being uploaded.
     *
     * @param Classement      $object          The object the upload is attached to
     * @param PropertyMapping $propertyMapping The mapping to use to manipulate the given object
     *
     * @return string The directory name
     */
    public function directoryName($object, PropertyMapping $propertyMapping): string
    {
        $fiche = $object->getFiche();

        return (string) $fiche->getId();
    }
}
