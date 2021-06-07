<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Entity\User;
use AcMarche\Bottin\Form\Security\UtilisateurEditType;
use AcMarche\Bottin\Form\Security\UtilisateurPasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/utilisateur/password")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class PasswordController extends AbstractController
{
    /**
     * Displays a form to edit an existing Utilisateur utilisateur.
     *
     * @Route("/{id}/password", name="bottin_utilisateur_password", methods={"GET","POST"})
     *
     * @todo
     */
    public function passord(Request $request, User $user): Response
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(UtilisateurEditType::class, $user);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('bottin_utilisateur');
        }

        return $this->render(
            '@AcMarcheBottin/utilisateur/password.html.twig',
            [
                'utilisateur' => $user,
                'edit_form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing categorie entity.
     *
     * @Route("/password/{id}", name="bottin_utilisateur_password", methods={"GET","POST"})
     */
    public function password(Request $request, User $user, UserPasswordHasherInterface $userPasswordEncoder): Response
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(UtilisateurPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $userPasswordEncoder->hashPassword($user, $form->getData()->getPlainPassword());
            $user->setPassword($password);
            $em->flush();

            $this->addFlash('success', 'Mot de passe changé');

            return $this->redirectToRoute('bottin_utilisateur_show', ['id' => $user->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/utilisateur/password.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }
}
