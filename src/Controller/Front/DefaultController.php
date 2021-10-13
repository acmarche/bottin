<?php

namespace AcMarche\Bottin\Controller\Front;

use AcMarche\Bottin\Bce\Repository\ApiCbeRepository;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\TokenRepository;
use AcMarche\Bottin\Token\Form\TokenPasswordType;
use AcMarche\Bottin\Utils\SortUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    private CategoryRepository $categoryRepository;
    private TokenRepository $tokenRepository;
    /**
     * @var \AcMarche\Bottin\Bce\Repository\ApiCbeRepository
     */
    private ApiCbeRepository $apiRepository;

    public function __construct(
        CategoryRepository $categoryRepository,
        TokenRepository $tokenRepository,
        ApiCbeRepository $apiRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->tokenRepository = $tokenRepository;
        $this->apiRepository = $apiRepository;
    }

    /**
     * @Route("/", name="bottin_front_home")
     */
    public function index(): Response
    {
        $categories = $this->categoryRepository->getRootNodes();
        $categories = SortUtils::sortCategories($categories);
        foreach ($categories as $rootNode) {
            $data[] = $this->categoryRepository->getTree($rootNode->getRealMaterializedPath());
        }

        return $this->render(
            '@AcMarcheBottin/front/default/index.html.twig',
            [
                'categories' => $data,
            ]
        );
    }


    /**
     * @Route("/updateFiche", name="bottin_backend_password", methods={"GET","POST"})
     */
    public function tokenPassword(Request $request): Response
    {
        $form = $this->createForm(TokenPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->get('password')->getData();
            if ($token = $this->tokenRepository->findOneByPassword($data)) {
                return $this->redirectToRoute('bottin_backend_fiche_show', ['uuid' => $token->getUuid()]);
            }
            $this->addFlash('danger', 'Fiche non trouvée');

            return $this->redirectToRoute('bottin_backend_password');
        }

        return $this->render(
            '@AcMarcheBottin/backend/token/password.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
