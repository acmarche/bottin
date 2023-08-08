<?php

namespace AcMarche\Bottin\Controller\Backend;

use AcMarche\Bottin\Classement\Form\ClassementSimpleType;
use AcMarche\Bottin\Classement\Handler\ClassementHandler;
use AcMarche\Bottin\Classement\Message\ClassementCreated;
use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Utils\PathUtils;
use AcMarche\Bottin\Utils\SortUtils;
use Exception;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Classement controller.
 */
#[Route(path: '/backend/classement')]
class ClassementController extends AbstractController
{
    public function __construct(private readonly ClassementRepository $classementRepository, private readonly ClassementHandler $classementHandler, private readonly CategoryRepository $categoryRepository, private readonly PathUtils $pathUtils, private readonly MessageBusInterface $messageBus)
    {
    }

    #[Route(path: '/edit/{uuid}', name: 'bottin_backend_classement_edit', methods: ['GET', 'POST'])]
    #[IsGranted('TOKEN_EDIT', subject: 'token')]
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
                $classement = $this->classementHandler->handleNewClassement($fiche, $categoryId);
                $this->messageBus->dispatch(new ClassementCreated($fiche->getId(), $classement->getId()));
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
}
