<?php

namespace AcMarche\Bottin\Controller\Backend;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Fiche\Form\Backend\FormUtils;
use AcMarche\Bottin\Fiche\Message\FicheUpdated;
use AcMarche\Bottin\History\HistoryUtils;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Security\Voter\TokenVoter;
use AcMarche\Bottin\Utils\PathUtils;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/backend/fiche')]
class FicheController extends AbstractController
{
    public function __construct(
        private readonly PathUtils $pathUtils,
        private readonly ClassementRepository $classementRepository,
        private readonly FormUtils $formUtils,
        private readonly FicheRepository $ficheRepository,
        private readonly HistoryUtils $historyUtils,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    #[Route(path: '/{uuid}', name: 'bottin_backend_fiche_show', methods: ['GET'])]
    #[IsGranted('TOKEN_EDIT', subject: 'token')]
    public function show(Token $token): Response
    {
        if (!$this->isGranted(TokenVoter::TOKEN_EDIT, $token)) {
            $this->addFlash('danger', 'Page expirée');

            return $this->redirectToRoute('bottin_front_home');
        }

        $fiche = $token->fiche;
        $classements = $this->classementRepository->getByFiche($fiche);
        $classements = $this->pathUtils->setPathForClassements($classements);

        return $this->render(
            '@AcMarcheBottin/backend/fiche/show.html.twig',
            [
                'fiche' => $fiche,
                'token' => $token,
                'classements' => $classements,
            ]
        );
    }

    #[Route(path: '/{uuid}/edit/{etape}', name: 'bottin_backend_fiche_edit', methods: ['GET', 'POST'])]
    #[IsGranted('TOKEN_EDIT', subject: 'token')]
    public function edit(Request $request, Token $token, int $etape = 1): Response
    {
        $fiche = $token->fiche;
        if (0 !== $etape) {
            $fiche->etape = $etape;
        }

        $oldAdresse = $fiche->getRue().' '.$fiche->getNumero().' '.$fiche->getLocalite();
        $form = $this->formUtils->createFormByEtape($fiche);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->historyUtils->diffFiche($fiche);
            } catch (Exception) {
                //  $this->addFlash('danger', 'error '.$e->getMessage());
            }

            $this->ficheRepository->flush();
            $this->messageBus->dispatch(new FicheUpdated($fiche->getId(), $oldAdresse));

            $this->addFlash('success', 'La fiche a bien été modifiée');
            $etape = $fiche->etape + 1;

            return $this->redirectToRoute(
                'bottin_backend_fiche_edit',
                ['uuid' => $token->getUuid(), 'etape' => $etape]
            );
        }

        return $this->render(
            '@AcMarcheBottin/backend/fiche/edit.html.twig',
            [
                'fiche' => $fiche,
                'token' => $token,
                'etape' => $fiche->etape,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'bottin_backend_fiche_delete', methods: ['POST'])]
    #[IsGranted('TOKEN_EDIT', subject: 'token')]
    public function delete(Request $request, Fiche $fiche): RedirectResponse
    {
        return $this->redirectToRoute('bottin_front_home');
    }
}
