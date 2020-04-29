<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\FicheImage;
use AcMarche\Bottin\Form\FicheImageType;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Repository\ImageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Vich\UploaderBundle\Handler\UploadHandler;

/**
 * Image controller.
 *
 * @Route("/image")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class ImageController extends AbstractController
{
    /**
     * @var UploadHandler
     */
    private $uploadHandler;
    /**
     * @var ImageRepository
     */
    private $imageRepository;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        ImageRepository $imageRepository,
        UploadHandler $uploadHandler,
        SerializerInterface $serializer
    ) {
        $this->uploadHandler = $uploadHandler;
        $this->imageRepository = $imageRepository;
        $this->serializer = $serializer;
    }

    /**
     * Displays a form to create a new Image entity.
     *
     * @Route("/new/{id}", name="bottin_image_new", methods={"GET", "POST"})
     */
    public function new(Fiche $fiche, Request $request)
    {
        $entity = new FicheImage($fiche);

        $form = $this->createForm(
            FicheImageType::class,
            $entity,
            [
                'action' => $this->generateUrl('bottin_image_upload', ['id' => $fiche->getId()]),
            ]
        );

        return $this->render(
            '@AcMarcheBottin/image/new.html.twig',
            [
                'fiche' => $fiche,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/upload/{id}", name="bottin_image_upload")
     *
     */
    public function upload(Request $request, Fiche $fiche)
    {
        $image = new FicheImage($fiche);
        /**
         * @var UploadedFile $file
         */
        $file = $request->files->get('file');

        $nom = str_replace('.'.$file->getClientOriginalExtension(), '', $file->getClientOriginalName());
        $image->setMime($file->getMimeType());
        $image->setImageName($file->getClientOriginalName());
        $image->setImage($file);

        try {
            $this->uploadHandler->upload($image, 'image');
        } catch (\Exception $exception) {
            return $this->render(
                '@AcMarcheBottin/upload/_response_fail.html.twig',
                ['error' => $exception->getMessage()]
            );
        }

        $this->imageRepository->persist($image);
        $this->imageRepository->flush();

        return $this->render('@AcMarcheBottin/upload/_response_ok.html.twig');
    }

    /**
     * Finds and displays a Image entity.
     *
     * @Route("/{id}", name="bottin_image_show", methods={"GET"})
     */
    public function show(FicheImage $ficheImage)
    {
        $data = json_decode($this->serializer->serialize($ficheImage, 'json', ['group2']), true);

        return $this->render(
            '@AcMarcheBottin/image/show.html.twig',
            [
                'image' => $ficheImage,
                'fiche' => $ficheImage->getFiche(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="bottin_image_delete", methods={"DELETE"})
     */
    public function delete(Request $request, FicheImage $ficheImage): Response
    {
        $fiche = $ficheImage->getFiche();
        if ($this->isCsrfTokenValid('delete'.$ficheImage->getId(), $request->request->get('_token'))) {
            $this->imageRepository->remove($ficheImage);
            $this->imageRepository->flush();
            $this->addFlash('success', "L'image a bien été supprimée");
        }

        return $this->redirect($this->generateUrl('bottin_fiche_show', ['id' => $fiche->getId()]));
    }


}
