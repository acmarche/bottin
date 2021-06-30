<?php

namespace AcMarche\Bottin\Controller\Backend;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Fiche\Message\FicheUpdated;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Security\Voter\TokenVoter;
use AcMarche\Bottin\Service\FormUtils;
use AcMarche\Bottin\Utils\PathUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Fiche controller.
 *
 * @Route("/backend/fiche")
 */
class FicheController extends AbstractController
{
    private ClassementRepository $classementRepository;
    private PathUtils $pathUtils;
    private FormUtils $formUtils;
    private FicheRepository $ficheRepository;

    public function __construct(
        PathUtils $pathUtils,
        ClassementRepository $classementRepository,
        FormUtils $formUtils,
        FicheRepository $ficheRepository
    ) {
        $this->classementRepository = $classementRepository;
        $this->pathUtils = $pathUtils;
        $this->formUtils = $formUtils;
        $this->ficheRepository = $ficheRepository;
    }

    /**
     * @Route("/{uuid}", name="bottin_backend_fiche_show", methods={"GET"})
     * @IsGranted("TOKEN_EDIT", subject="token")
     */
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

    /**
     * @Route("/{uuid}/edit/{etape}", name="bottin_backend_fiche_edit", methods={"GET", "POST"})
     * @IsGranted("TOKEN_EDIT", subject="token")
     */
    public function edit(Request $request, Token $token, int $etape = 1): Response
    {
        $fiche = $token->getFiche();
        if ($etape) {
            $fiche->setEtape($etape);
        }
        $oldAdresse = $fiche->getRue().' '.$fiche->getNumero().' '.$fiche->getLocalite();

        $form = $this->formUtils->createFormByEtape($fiche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ficheRepository->flush();
            $this->dispatchMessage(new FicheUpdated($fiche->getId(), $oldAdresse));

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

    /**
     * @Route("/{id}", name="bottin_backend_fiche_delete", methods={"POST"})
     * @IsGranted("TOKEN_EDIT", subject="token")
     */
    public function delete(Request $request, Fiche $fiche): RedirectResponse
    {
        return $this->redirectToRoute('bottin_front_home');
    }

}
