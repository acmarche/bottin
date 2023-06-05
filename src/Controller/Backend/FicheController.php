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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Fiche controller.
 */
#[Route(path: '/backend/fiche')]
class FicheController extends AbstractController
{
    public function __construct(
        private PathUtils $pathUtils,
        private ClassementRepository $classementRepository,
        private FormUtils $formUtils,
        private FicheRepository $ficheRepository,
        private HistoryUtils $historyUtils,
        private MessageBusInterface $messageBus
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
        $fiche = $token->getFiche();
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
        $fiche = $token->getFiche();
        if (0 !== $etape) {
            $fiche->setEtape($etape);
        }
        $oldAdresse = $fiche->getRue().' '.$fiche->getNumero().' '.$fiche->getLocalite();
        $form = $this->formUtils->createFormByEtape($fiche);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $this->historyUtils->diffFiche($fiche);
            } catch (Exception $e) {
              //  $this->addFlash('danger', 'error '.$e->getMessage());
            }

            $this->ficheRepository->flush();
            $this->messageBus->dispatch(new FicheUpdated($fiche->getId(), $oldAdresse));

            $this->addFlash('success', 'La fiche a bien été modifiée');
            $etape = $fiche->getEtape() + 1;

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
                'etape' => $fiche->getEtape(),
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
