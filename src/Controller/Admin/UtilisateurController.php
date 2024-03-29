<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\User;
use AcMarche\Bottin\Repository\UserRepository;
use AcMarche\Bottin\User\Form\UtilisateurEditType;
use AcMarche\Bottin\User\Form\UtilisateurType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/utilisateur')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class UtilisateurController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly ManagerRegistry $managerRegistry
    ) {
    }

    #[Route(path: '/', name: 'bottin_admin_utilisateur', methods: ['GET'])]
    public function index(): Response
    {
        $users = $this->userRepository->findBy([], ['nom' => 'ASC']);

        return $this->render(
            '@AcMarcheBottin/admin/utilisateur/index.html.twig',
            [
                'users' => $users,
            ]
        );
    }

    #[Route(path: '/new', name: 'bottin_admin_utilisateur_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UtilisateurType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->password =
                $this->userPasswordHasher->hashPassword($user, $form->getData()->plainPassword);
            $this->userRepository->insert($user);

            $this->addFlash('success', "L'utilisateur a bien été ajouté");

            return $this->redirectToRoute('bottin_admin_utilisateur');
        }

        return $this->render(
            '@AcMarcheBottin/admin/utilisateur/new.html.twig',
            [
                'utilisateur' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'bottin_admin_utilisateur_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render(
            '@AcMarcheBottin/admin/utilisateur/show.html.twig',
            [
                'utilisateur' => $user,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'bottin_admin_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $editForm = $this->createForm(UtilisateurEditType::class, $user);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->userRepository->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('bottin_admin_utilisateur');
        }

        return $this->render(
            '@AcMarcheBottin/admin/utilisateur/edit.html.twig',
            [
                'utilisateur' => $user,
                'form' => $editForm->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'bottin_admin_utilisateur_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->managerRegistry->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a été supprimé');
        }

        return $this->redirectToRoute('bottin_admin_utilisateur');
    }
}
