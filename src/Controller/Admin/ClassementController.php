<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Classement\Form\ClassementType;
use AcMarche\Bottin\Classement\Handler\ClassementHandler;
use AcMarche\Bottin\Classement\Message\ClassementCreated;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Utils\PathUtils;
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
#[Route(path: '/admin/classement')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class ClassementController extends AbstractController
{
    public function __construct(private ClassementRepository $classementRepository, private ClassementHandler $classementHandler, private CategoryRepository $categoryRepository, private PathUtils $pathUtils, private MessageBusInterface $messageBus)
    {
    }

    /**
     * Displays a form to create a new classement entity.
     */
    #[Route(path: '/edit/{id}', name: 'bottin_admin_classement_new', methods: ['GET', 'POST'])]
    public function edit(Fiche $fiche, Request $request): Response
    {
        $form = $this->createForm(ClassementType::class);
        $classements = $this->classementRepository->getByFiche($fiche);
        $classements = $this->pathUtils->setPathForClassements($classements);
        $roots = $this->categoryRepository->getRootNodes();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $categoryId = (int) $data['categorySelected'];

            try {
                $classement = $this->classementHandler->handleNewClassement($fiche, $categoryId);
                $this->messageBus->dispatch(new ClassementCreated($fiche->getId(), $classement->getId()));

                return $this->redirectToRoute('bottin_admin_classement_new', ['id' => $fiche->getId()]);
            } catch (Exception $e) {
                $this->addFlash('danger', $e->getMessage());

                return $this->redirectToRoute('bottin_admin_classement_new', ['id' => $fiche->getId()]);
            }
        }

        return $this->render(
            '@AcMarcheBottin/admin/classement/edit.html.twig',
            [
                'fiche' => $fiche,
                'classements' => $classements,
                'roots' => $roots,
                'form' => $form->createView(),
            ]
        );
    }
}
