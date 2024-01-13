<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\FicheImage;
use AcMarche\Bottin\Form\ImageDropZoneType;
use AcMarche\Bottin\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Vich\UploaderBundle\Handler\UploadHandler;

#[Route(path: '/admin/image')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class ImageController extends AbstractController
{
    public function __construct(
        private readonly ImageRepository $imageRepository,
        private readonly UploadHandler $uploadHandler
    ) {
    }

    #[Route(path: '/new/{id}', name: 'bottin_admin_image_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Fiche $fiche): Response
    {
        $form = $this->createForm(ImageDropZoneType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var UploadedFile[] $data
             */
            $data = $form->getData();
            foreach ($data['file'] as $file) {
                if ($file instanceof UploadedFile) {
                    $this->treatmentFile($file, $fiche);
                }
            }

            return $this->redirectToRoute('bottin_admin_fiche_show', ['id' => $fiche->getId()]);
        }

        // $images = $this->fileHelper->getImages($association);

        return $this->render(
            '@AcMarcheBottin/admin/image/new.html.twig',
            [
                'fiche' => $fiche,
                'form' => $form->createView(),
            ]
        );
    }

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
            $this->addFlash('danger', 'Erreur upload image: '.$exception->getMessage());
        }

        $this->imageRepository->persist($ficheImage);
        $this->imageRepository->flush();
    }

    #[Route(path: '/{id}', name: 'bottin_admin_image_show', methods: ['GET'])]
    public function show(FicheImage $ficheImage): Response
    {
        return $this->render(
            '@AcMarcheBottin/admin/image/show.html.twig',
            [
                'image' => $ficheImage,
                'fiche' => $ficheImage->fiche,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'bottin_admin_image_delete', methods: ['POST'])]
    public function delete(Request $request, FicheImage $ficheImage): RedirectResponse
    {
        $fiche = $ficheImage->fiche;
        if ($this->isCsrfTokenValid('delete'.$ficheImage->getId(), $request->request->get('_token'))) {
            $this->imageRepository->remove($ficheImage);
            $this->imageRepository->flush();
            $this->addFlash('success', "L'image a bien été supprimée");
        }

        return $this->redirectToRoute('bottin_admin_fiche_show', ['id' => $fiche->getId()]);
    }
}
