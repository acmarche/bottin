<?php

namespace AcMarche\Bottin\Controller\Backend;

use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Form\ContactType;
use AcMarche\Bottin\Mailer\MailFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/backend')]
class DefaultController extends AbstractController
{
    public function __construct(private readonly MailerInterface $mailer, private readonly MailFactory $mailFactory)
    {
    }

    #[Route(path: '/', name: 'bottin_backend_home')]
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheBottin/backend/default/index.html.twig',
            [
            ]
        );
    }

    #[Route(path: '/contact/{uuid}', name: 'bottin_backend_contact')]
    #[IsGranted('TOKEN_EDIT', subject: 'token')]
    public function contact(Request $request, Token $token): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = $this->mailFactory->mailContact($data['nom'], $data['email'], $data['message']);
            try {
                $this->mailer->send($email);
                $this->addFlash('success', 'Votre message a bien été envoyé');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('danger', "Erreur lors de l'envoie du message: ".$e->getMessage());
            }

            return $this->redirectToRoute('bottin_backend_fiche_show', ['uuid' => $token->uuid]);
        }

        return $this->render(
            '@AcMarcheBottin/backend/default/contact.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
