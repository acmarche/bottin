<?php

namespace AcMarche\Bottin\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_BOTTIN_ADMIN')]
#[Route(path: '/admin')]
class DefaultController extends AbstractController
{
    #[Route(path: '/parameters', name: 'bottin_admin_parameter_index')]
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheBottin/admin/parameter/index.html.twig',
            [

            ]
        );
    }
}
