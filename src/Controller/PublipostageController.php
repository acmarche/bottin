<?php

namespace AcMarche\Bottin\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Publipostage controller.
 *
 * @Route("/publipostage")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class PublipostageController extends AbstractController
{
    /**
     * @Route("/categories", name="bottin_publipostage", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('@AcMarcheBottin/publipostage/index.html.twig');
    }
}
