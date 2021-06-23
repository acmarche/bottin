<?php

namespace AcMarche\Bottin\Controller\Front;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Fiche\Message\FicheUpdated;
use AcMarche\Bottin\Form\Fiche\FicheActiviteType;
use AcMarche\Bottin\Form\FicheType;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Service\HoraireService;
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
 * @Route("/fiche")
 */
class FicheController extends AbstractController
{
    private FicheRepository $ficheRepository;
    private HoraireService $horaireService;
    private ClassementRepository $classementRepository;
    private PathUtils $pathUtils;

    public function __construct(
        PathUtils $pathUtils,
        ClassementRepository $classementRepository,
        FicheRepository $ficheRepository,
        HoraireService $horaireService
    ) {
        $this->ficheRepository = $ficheRepository;
        $this->horaireService = $horaireService;
        $this->classementRepository = $classementRepository;
        $this->pathUtils = $pathUtils;
    }

    /**
     * Finds and displays a Fiche fiche.
     *
     * @Route("/{id}", name="bottin_fiche_show", methods={"GET"})
     */
    public function show(Fiche $fiche): Response
    {
        $classements = $this->classementRepository->getByFiche($fiche);
        $classements = $this->pathUtils->setPathForClassements($classements);

        return $this->render(
            '@AcMarcheBottin/front/fiche/show.html.twig',
            [
                'fiche' => $fiche,
                'classements' => $classements,
            ]
        );
    }

    /**
     * Displays a form to edit an existing Fiche fiche.
     *
     * @Route("/{uuid}/edit", name="bottin_fiche_edit", methods={"GET", "POST"})
     * IsGranted("POST_EDIT", subject="token")
     */
    public function edit(Request $request, Token $token): Response
    {
        if (!$this->isGranted('POST_EDIT', $token)) {
            $this->addFlash('danger', 'Page expirée');

            return $this->redirectToRoute('bottin_home');
        }

        //  $this->denyAccessUnlessGranted('POST_EDIT', $token);

        $fiche = $token->getFiche();
        $oldAdresse = $fiche->getRue().' '.$fiche->getNumero().' '.$fiche->getLocalite();

        $form = $this->createForm(FicheActiviteType::class, $fiche);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->ficheRepository->flush();

            $this->dispatchMessage(new FicheUpdated($fiche->getId(), $oldAdresse));

            $this->addFlash('success', 'La fiche a bien été modifiée');

            return $this->redirectToRoute('bottin_fiche_show', ['id' => $fiche->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/backend/fiche/edit.html.twig',
            [
                'fiche' => $fiche,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="bottin_fiche_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Fiche $fiche): RedirectResponse
    {
        return $this->redirectToRoute('bottin_home');
    }
}
