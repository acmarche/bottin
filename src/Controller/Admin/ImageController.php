<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\FicheImage;
use AcMarche\Bottin\Fiche\Form\FicheImageType;
use AcMarche\Bottin\Repository\ImageRepository;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\UploadHandler;

/**
 * Image controller.
 *
 * @Route("/admin/image")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class ImageController extends AbstractController
{
    private UploadHandler $uploadHandler;
    private ImageRepository $imageRepository;

    public function __construct(
        ImageRepository $imageRepository,
        UploadHandler $uploadHandler
    ) {
        $this->uploadHandler = $uploadHandler;
        $this->imageRepository = $imageRepository;
    }

    /**
     * Displays a form to create a new Image entity.
     *
     * @Route("/new/{id}", name="bottin_admin_image_new", methods={"GET", "POST"})
     */
    public function new(Fiche $fiche): Response
    {
        $ficheImage = new FicheImage($fiche);

        $form = $this->createForm(
            FicheImageType::class,
            $ficheImage,
            [
                'action' => $this->generateUrl('bottin_admin_image_upload', ['id' => $fiche->getId()]),
            ]
        );

        return $this->render(
            '@AcMarcheBottin/admin/image/new.html.twig',
            [
                'fiche' => $fiche,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/upload/{id}", name="bottin_admin_image_upload")
     */
    public function upload(Request $request, Fiche $fiche): Response
    {
        $ficheImage = new FicheImage($fiche);
        /**
         * @var UploadedFile $file
         */
        $file = $request->files->get('file');

        $nom = str_replace('.'.$file->getClientOriginalExtension(), '', $file->getClientOriginalName());
        $ficheImage->setMime($file->getMimeType());
        $ficheImage->setImageName($file->getClientOriginalName());
        $ficheImage->setImage($file);

        try {
            $this->uploadHandler->upload($ficheImage, 'image');
        } catch (Exception $exception) {
            return $this->render(
                '@AcMarcheBottin/admin/upload/_response_fail.html.twig',
                ['error' => $exception->getMessage()]
            );
        }

        $this->imageRepository->persist($ficheImage);
        $this->imageRepository->flush();

        return $this->render('@AcMarcheBottin/admin/upload/_response_ok.html.twig');
    }

    /**
     * Finds and displays a Image entity.
     *
     * @Route("/{id}", name="bottin_admin_image_show", methods={"GET"})
     */
    public function show(FicheImage $ficheImage): Response
    {
        return $this->render(
            '@AcMarcheBottin/admin/image/show.html.twig',
            [
                'image' => $ficheImage,
                'fiche' => $ficheImage->getFiche(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="bottin_admin_image_delete", methods={"POST"})
     */
    public function delete(Request $request, FicheImage $ficheImage): RedirectResponse
    {
        $fiche = $ficheImage->getFiche();
        if ($this->isCsrfTokenValid('delete'.$ficheImage->getId(), $request->request->get('_token'))) {
            $this->imageRepository->remove($ficheImage);
            $this->imageRepository->flush();
            $this->addFlash('success', "L'image a bien été supprimée");
        }

        return $this->redirect($this->generateUrl('bottin_admin_fiche_show', ['id' => $fiche->getId()]));
    }
}
