<?php

namespace AcMarche\Bottin\Controller\Backend;

use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Form\ContactType;
use AcMarche\Bottin\Mailer\Mailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @Route("/backend")
 */
class DefaultController extends AbstractController
{
    private Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @Route("/", name="bottin_backend_home")
     */
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheBottin/backend/default/index.html.twig',
            [
            ]
        );
    }

    /**
     * @Route("/contact/{uuid}", name="bottin_backend_contact")
     * @IsGranted("TOKEN_EDIT", subject="token")
     */
    public function contact(Request $request, Token $token): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            try {
                $this->mailer->sendContact($data['nom'], $data['email'], $data['message']);
                $this->addFlash('success', 'Votre message a bien été envoyé');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('danger', 'Erreur lors de l\'envoie du message: '.$e->getMessage());
            }

            return $this->redirectToRoute('bottin_backend_fiche_show', ['uuid' => $token->getUuid()]);
        }

        return $this->render(
            '@AcMarcheBottin/backend/default/contact.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
