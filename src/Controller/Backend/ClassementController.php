<?php

namespace AcMarche\Bottin\Controller\Backend;

use AcMarche\Bottin\Classement\Handler\ClassementHandler;
use AcMarche\Bottin\Classement\Message\ClassementDeleted;
use AcMarche\Bottin\Classement\Message\ClassementUpdated;
use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Form\ClassementSimpleType;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Utils\PathUtils;
use AcMarche\Bottin\Utils\SortUtils;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Classement controller.
 *
 * @Route("/backend/classement")
 */
class ClassementController extends AbstractController
{
    private ClassementRepository $classementRepository;
    private CategoryRepository $categoryRepository;
    private PathUtils $pathUtils;
    private ClassementHandler $classementHandler;

    public function __construct(
        ClassementRepository $classementRepository,
        ClassementHandler $classementHandler,
        CategoryRepository $categoryRepository,
        PathUtils $pathUtils
    ) {
        $this->classementRepository = $classementRepository;
        $this->categoryRepository = $categoryRepository;
        $this->pathUtils = $pathUtils;
        $this->classementHandler = $classementHandler;
    }

    /**
     * @Route("/edit/{uuid}", name="bottin_backend_classement_edit", methods={"GET", "POST"})
     * @IsGranted("TOKEN_EDIT", subject="token")
     */
    public function edit(Token $token, Request $request): Response
    {
        $fiche = $token->getFiche();
        $form = $this->createForm(ClassementSimpleType::class);

        $classements = $this->classementRepository->getByFiche($fiche);
        $classements = $this->pathUtils->setPathForClassements($classements);
        $roots = $this->categoryRepository->getRootNodes();
        $roots = SortUtils::sortCategories($roots);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $categoryId = (int) $data['categorySelected'];

            try {
                $this->classementHandler->handleNewClassement($fiche, $categoryId);
                $this->dispatchMessage(new ClassementUpdated($fiche->getId()));
                $this->addFlash('success', 'Le classement a bien été ajouté');
            } catch (Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }

            return $this->redirectToRoute('bottin_backend_classement_edit', ['uuid' => $token->getUuid()]);
        }

        return $this->render(
            '@AcMarcheBottin/backend/classement/edit.html.twig',
            [
                'fiche' => $fiche,
                'token' => $token,
                'classements' => $classements,
                'roots' => $roots,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/removeclassment/{uuid}", name="bottin_backend_ajax_remove_classement", methods={"POST"})
     * @IsGranted("TOKEN_EDIT", subject="token")
     */
    public function removeClassement(Request $request, Token $token): Response
    {
        $classementId = (int) $request->request->get('classementId');
        $classement = $this->classementRepository->find($classementId);

        if (null === $classement) {
            $error = 'classement non trouvé';
            $template = $this->renderView('@AcMarcheBottin/admin/ajax/error.html.twig', ['error' => $error]);

            return new Response($template);
        }

        $ficheFromClassement = $classement->getFiche();
        $fiche = $token->getFiche();

        if ($fiche->getId() != $ficheFromClassement->getId()) {
            $txt = 'Non authorisé';

            return new Response($txt);
        }

        $this->classementRepository->remove($classement);
        $this->classementRepository->flush();

        $this->dispatchMessage(new ClassementDeleted($fiche->getId()));

        $classements = $this->classementRepository->getByFiche($fiche);
        $classements = $this->pathUtils->setPathForClassements($classements);

        $template = $this->renderView(
                '@AcMarcheBottin/backend/classement/_list.html.twig',
                ['classements' => $classements]
            );

        return new Response($template);
    }
}
