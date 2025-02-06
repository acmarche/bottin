<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Classement\Form\ClassementByAutocompleteType;
use AcMarche\Bottin\Classement\Form\ClassementType;
use AcMarche\Bottin\Classement\Handler\ClassementHandler;
use AcMarche\Bottin\Classement\Message\ClassementCreated;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Utils\PathUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/classement')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class ClassementController extends AbstractController
{
    public function __construct(
        private readonly ClassementRepository $classementRepository,
        private readonly ClassementHandler $classementHandler,
        private readonly CategoryRepository $categoryRepository,
        private readonly PathUtils $pathUtils,
        private readonly MessageBusInterface $messageBus,
    ) {}

    #[Route(path: '/edit/{id}', name: 'bottin_admin_classement_new', methods: ['GET', 'POST'])]
    public function edit(Fiche $fiche, Request $request): Response
    {
        $formAutocomplete = $this->createForm(ClassementByAutocompleteType::class);
        $formNavigation = $this->createForm(ClassementType::class);

        $formNavigation->handleRequest($request);
        if ($formNavigation->isSubmitted() && $formNavigation->isValid()) {
            $data = $formNavigation->getData();
            $categoryId = (int)$data['categorySelected'];
            try {
                $classement = $this->classementHandler->handleNewClassement($fiche, $categoryId);
                $this->messageBus->dispatch(new ClassementCreated($fiche->getId(), $classement->getId()));
                $category = $this->categoryRepository->find($categoryId);
                $this->addFlash('success', 'Ajouté dans '.$category->name);
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }

            return $this->redirectToRoute('bottin_admin_classement_new', ['id' => $fiche->getId()]);
        }

        $formAutocomplete->handleRequest($request);
        if ($formAutocomplete->isSubmitted() && $formAutocomplete->isValid()) {
            $data = $formAutocomplete->getData();
            $categories = $data['categories'];
            foreach ($categories as $category) {
                try {
                    $classement = $this->classementHandler->handleNewClassement($fiche, $category->getId());
                    $this->messageBus->dispatch(new ClassementCreated($fiche->getId(), $classement->getId()));
                    $this->addFlash('success', 'Ajouté dans '.$category->name);
                } catch (\Exception $e) {
                    $this->addFlash('danger', $e->getMessage());
                }
            }

            return $this->redirectToRoute('bottin_admin_classement_new', ['id' => $fiche->getId()]);
        }

        $classements = $this->classementRepository->getByFiche($fiche);
        $classements = $this->pathUtils->setPathForClassements($classements);

        $roots = $this->categoryRepository->getRootNodes();
        $response = new Response(null, $formNavigation->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@AcMarcheBottin/admin/classement/edit.html.twig',
            [
                'fiche' => $fiche,
                'classements' => $classements,
                'roots' => $roots,
                'formNavigation' => $formNavigation,
                'formAutocomplete' => $formAutocomplete,
            ],
            $response,
        );
    }
}
