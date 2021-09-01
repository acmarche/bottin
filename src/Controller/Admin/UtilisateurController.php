<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\User;
use AcMarche\Bottin\Form\Security\UtilisateurEditType;
use AcMarche\Bottin\Form\Security\UtilisateurType;
use AcMarche\Bottin\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/utilisateur")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class UtilisateurController extends AbstractController
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * Lists all Utilisateur entities.
     *
     * @Route("/", name="bottin_admin_utilisateur", methods={"GET"})
     */
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

    /**
     * Displays a form to create a new Utilisateur utilisateur.
     *
     * @Route("/new", name="bottin_admin_utilisateur_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(UtilisateurType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $this->userPasswordHasher->hashPassword($user, $form->getData()->getPlainPassword())
            );
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

    /**
     * Finds and displays a Utilisateur utilisateur.
     *
     * @Route("/{id}", name="bottin_admin_utilisateur_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render(
            '@AcMarcheBottin/admin/utilisateur/show.html.twig',
            [
                'utilisateur' => $user,
            ]
        );
    }

    /**
     * Displays a form to edit an existing Utilisateur utilisateur.
     *
     * @Route("/{id}/edit", name="bottin_admin_utilisateur_edit", methods={"GET", "POST"})
     */
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

    /**
     * Deletes a Utilisateur utilisateur.
     *
     * @Route("/{id}", name="bottin_admin_utilisateur_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a été supprimé');
        }

        return $this->redirectToRoute('bottin_admin_utilisateur');
    }
}
