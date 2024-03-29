<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\User;
use AcMarche\Bottin\User\Form\UtilisateurEditType;
use AcMarche\Bottin\User\Form\UtilisateurPasswordType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/utilisateur/password')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class PasswordController extends AbstractController
{
    public function __construct(private readonly ManagerRegistry $managerRegistry)
    {
    }

    /**
     * Displays a form to edit an existing Utilisateur utilisateur.
     *
     * @todo
     */
    #[Route(path: '/{id}/password', name: 'bottin_admin_utilisateur_password', methods: ['GET', 'POST'])]
    public function passord(Request $request, User $user): Response
    {
        $em = $this->managerRegistry->getManager();
        $editForm = $this->createForm(UtilisateurEditType::class, $user);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('bottin_admin_utilisateur');
        }

        return $this->render(
            '@AcMarcheBottin/admin/utilisateur/password.html.twig',
            [
                'utilisateur' => $user,
                'edit_form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing categorie entity.
     */
    #[Route(path: '/password/{id}', name: 'bottin_admin_utilisateur_password', methods: ['GET', 'POST'])]
    public function password(Request $request, User $user, UserPasswordHasherInterface $userPasswordEncoder): Response
    {
        $em = $this->managerRegistry->getManager();
        $form = $this->createForm(UtilisateurPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $userPasswordEncoder->hashPassword($user, $form->getData()->getPlainPassword());
            $user->password = $password;
            $em->flush();

            $this->addFlash('success', 'Mot de passe changé');

            return $this->redirectToRoute('bottin_admin_utilisateur_show', ['id' => $user->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/admin/utilisateur/password.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }
}
