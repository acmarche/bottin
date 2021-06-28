<?php

namespace AcMarche\Bottin\Controller\Backend;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Service\FormUtils;
use AcMarche\Bottin\Service\HoraireService;
use AcMarche\Bottin\Utils\PathUtils;
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
    private FicheRepository $ficheRepository;
    private HoraireService $horaireService;
    private ClassementRepository $classementRepository;
    private PathUtils $pathUtils;
    private FormUtils $formUtils;

    public function __construct(
        PathUtils $pathUtils,
        ClassementRepository $classementRepository,
        FicheRepository $ficheRepository,
        HoraireService $horaireService,
        FormUtils $formUtils
    ) {
        $this->ficheRepository = $ficheRepository;
        $this->horaireService = $horaireService;
        $this->classementRepository = $classementRepository;
        $this->pathUtils = $pathUtils;
        $this->formUtils = $formUtils;
    }

    /**
     * Finds and displays a Fiche fiche.
     *
     * @Route("/{uuid}", name="bottin_backend_fiche_show", methods={"GET"})
     */
    public function show(Token $token): Response
    {
        if (!$this->isGranted('POST_EDIT', $token)) {
            $this->addFlash('danger', 'Page expirée');

            return $this->redirectToRoute('bottin_home');
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
     * Displays a form to edit an existing Fiche fiche.
     *
     * @Route("/{uuid}/edit/{etape}", name="bottin_backend_fiche_edit", methods={"GET", "POST"})
     * IsGranted("POST_EDIT", subject="token")
     */
    public function edit(Request $request, Token $token, int $etape = 1): Response
    {
        if (!$this->isGranted('POST_EDIT', $token)) {
            $this->addFlash('danger', 'Page expirée');

            return $this->redirectToRoute('bottin_home');
        }

        //  $this->denyAccessUnlessGranted('POST_EDIT', $token);

        $fiche = $token->getFiche();
        if ($etape) {
            $fiche->setEtape($etape);
        }
        $oldAdresse = $fiche->getRue().' '.$fiche->getNumero().' '.$fiche->getLocalite();

        $form = $this->formUtils->createFormByEtape($fiche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //      $this->ficheRepository->flush();

            //      $this->dispatchMessage(new FicheUpdated($fiche->getId(), $oldAdresse));

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
     * @Route("/{id}", name="bottin_backend_fiche_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Fiche $fiche): RedirectResponse
    {
        return $this->redirectToRoute('bottin_home');
    }

}
