<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\FicheImage;
use AcMarche\Bottin\Form\ImageDropZoneType;
use AcMarche\Bottin\Repository\ImageRepository;
use AcMarche\Bottin\Upload\UploadHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/image')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class ImageController extends AbstractController
{
    public function __construct(
        private readonly ImageRepository $imageRepository,
        private readonly UploadHelper $uploadHelper
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
                    try {
                        $this->uploadHelper->treatmentFile($file, $fiche);
                    } catch (\Exception $exception) {
                        $this->addFlash('danger', 'Erreur upload image: '.$exception->getMessage());
                    }
                }
            }

            return $this->redirectToRoute('bottin_admin_fiche_show', ['id' => $fiche->getId()]);
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@AcMarcheBottin/admin/image/new.html.twig',
            [
                'fiche' => $fiche,
                'form' => $form->createView(),
            ]
            , $response
        );
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
