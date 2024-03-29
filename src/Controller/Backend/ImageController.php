<?php

namespace AcMarche\Bottin\Controller\Backend;

use AcMarche\Bottin\Entity\FicheImage;
use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Fiche\Form\FicheImageType;
use AcMarche\Bottin\Form\ImageDropZoneType;
use AcMarche\Bottin\History\HistoryUtils;
use AcMarche\Bottin\Repository\ImageRepository;
use AcMarche\Bottin\Security\Voter\TokenVoter;
use AcMarche\Bottin\Upload\UploadHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/backend/image')]
class ImageController extends AbstractController
{
    public function __construct(
        private readonly ImageRepository $imageRepository,
        private readonly UploadHelper $uploadHelper,
        private readonly HistoryUtils $historyUtils
    ) {
    }

    #[Route(path: '/new/{uuid}', name: 'bottin_backend_image_edit', methods: ['GET', 'POST'])]
    #[IsGranted('TOKEN_EDIT', subject: 'token')]
    public function new(Request $request, Token $token): Response
    {
        $fiche = $token->fiche;
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
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK);

        return $this->render(
            '@AcMarcheBottin/backend/image/edit.html.twig',
            [
                'fiche' => $fiche,
                'token' => $token,
                'form' => $form->createView(),
            ]
            , $response
        );
    }

    #[Route(path: '/upload/{uuid}', name: 'bottin_backend_image_upload')]
    #[IsGranted('TOKEN_EDIT', subject: 'token')]
    public function upload(Request $request, Token $token): Response
    {
        $fiche = $token->fiche;
        $ficheImage = new FicheImage($fiche);
        /**
         * @var UploadedFile $file
         */
        $file = $request->files->get('file');
        $nom = str_replace('.'.$file->getClientOriginalExtension(), '', $file->getClientOriginalName());
        $ficheImage->mime = $file->getMimeType();
        $ficheImage->imageName = $file->getClientOriginalName();
        $ficheImage->setImage($file);
        try {
            $this->uploadHandler->upload($ficheImage, 'image');
        } catch (\Exception $exception) {
            return $this->render(
                '@AcMarcheBottin/admin/upload/_response_fail.html.twig',
                ['error' => $exception->getMessage()]
            );
        }

        $this->imageRepository->persist($ficheImage);
        $this->imageRepository->flush();

        $this->historyUtils->addImage($fiche, $ficheImage);

        return $this->render('@AcMarcheBottin/admin/upload/_response_ok.html.twig');
    }

    #[Route(path: '/{id}', name: 'bottin_backend_image_show', methods: ['GET'])]
    #[IsGranted('TOKEN_EDIT', subject: 'token')]
    public function show(FicheImage $ficheImage): Response
    {
        $fiche = $ficheImage->fiche;
        $token = $fiche->token;
        $this->isGranted(TokenVoter::TOKEN_EDIT, $token);

        return $this->render(
            '@AcMarcheBottin/admin/image/show.html.twig',
            [
                'image' => $ficheImage,
                'fiche' => $ficheImage->fiche,
            ]
        );
    }

    #[Route(path: '/', name: 'bottin_backend_image_delete', methods: ['POST'])]
    public function delete(Request $request): RedirectResponse
    {
        $imageId = (int)$request->request->get('imageid');
        if (0 === $imageId) {
            $this->addFlash('danger', 'Image non trouvée');

            return $this->redirectToRoute('bottin_front_home');
        }

        $ficheImage = $this->imageRepository->find($imageId);
        if (!$ficheImage instanceof FicheImage) {
            $this->addFlash('danger', 'Image non trouvée');

            return $this->redirectToRoute('bottin_front_home');
        }

        $fiche = $ficheImage->fiche;
        $token = $fiche->token;
        $this->isGranted(TokenVoter::TOKEN_EDIT, $token);
        if ($this->isCsrfTokenValid('deleteimage', $request->request->get('_token'))) {
            $this->imageRepository->remove($ficheImage);
            $this->imageRepository->flush();
            $this->addFlash('success', "L'image a bien été supprimée");
        }

        return $this->redirectToRoute('bottin_backend_image_edit', ['uuid' => $token->uuid]);
    }
}
