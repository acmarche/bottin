<?php

namespace AcMarche\Bottin\Upload;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\FicheImage;
use AcMarche\Bottin\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Handler\UploadHandler;

class UploadHelper
{
    public function __construct(
        private readonly ImageRepository $imageRepository,
        private readonly UploadHandler $uploadHandler
    ) {
    }

    /**
     * @param UploadedFile $file
     * @param Fiche $fiche
     * @return void
     * @throws \Exception
     */
    public function treatmentFile(UploadedFile $file, Fiche $fiche): void
    {
        $ficheImage = new FicheImage($fiche);
        $orignalName = preg_replace(
            '#.'.$file->guessClientExtension().'#',
            '',
            $file->getClientOriginalName()
        );
        $fileName = $orignalName.'-'.uniqid().'.'.$file->guessClientExtension();

        $ficheImage->mime = $file->getMimeType();
        $ficheImage->imageName = $fileName;
        $ficheImage->image = $file;
        try {
            $this->uploadHandler->upload($ficheImage, 'image');
        } catch (\Exception $exception) {
            throw new \Exception('Erreur upload image: '.$exception->getMessage());
        }

        $this->imageRepository->persist($ficheImage);
        $this->imageRepository->flush();
    }

}