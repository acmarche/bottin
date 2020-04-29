<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Entity\Demande;
use AcMarche\Bottin\Form\DemandeType;
use AcMarche\Bottin\Repository\DemandeRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Service\MailerBottin;
use AcMarche\Bottin\Utils\PropertyUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Demande controller.
 *
 * @Route("/demande")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class DemandeController extends AbstractController
{
    /**
     * @var DemandeRepository
     */
    private $demandeRepository;
    /**
     * @var FicheRepository
     */
    private $ficheRepository;
    /**
     * @var MailerBottin
     */
    private $mailerBottin;
    /**
     * @var PropertyUtil
     */
    private $propertyUtil;

    public function __construct(
        DemandeRepository $demandeRepository,
        FicheRepository $ficheRepository,
        MailerBottin $mailerBottin,
        PropertyUtil $propertyUtil
    ) {
        $this->demandeRepository = $demandeRepository;
        $this->ficheRepository = $ficheRepository;
        $this->mailerBottin = $mailerBottin;
        $this->propertyUtil = $propertyUtil;
    }

    /**
     * Lists all Demande entities.
     *
     * @Route("/", name="bottin_demande", methods={"GET"})
     */
    public function index()
    {
        $demandes = $this->demandeRepository->search();

        return $this->render(
            '@AcMarcheBottin/demande/index.html.twig',
            [
                'demandes' => $demandes,
            ]
        );
    }

    /**
     * Finds and displays a Demande entity.
     *
     * @Route("/{id}", name="bottin_demande_show", methods={"GET", "POST"})
     */
    public function show(Request $request, Demande $demande)
    {
        $fiche = $this->ficheRepository->find($demande->getFiche());

        if (!$fiche) {
            return $this->createNotFoundException('Fiche non trouvée');
        }

        $editForm = $this->createForm(DemandeType::class, $demande);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if ($demande->getTraiter()) {
                $this->addFlash('warning', 'Cette demande a déjà été traitée');

                return $this->redirectToRoute('bottin_demande_show', ['id' => $demande->getId()]);
            }

            $metas = $request->request->get('metas');
            if (0 == count($metas)) {
                $this->addFlash('danger', 'Il faut au moins un champ à modifier pour valider la demande');

                return $this->redirectToRoute('bottin_demande_show', ['id' => $demande->getId()]);
            }
            foreach ($metas as $champ => $value) {
                $set = 'set'.ucfirst($champ);
                $fiche->$set($value);
            }

            $demande->setTraiter(true);
            $demande->setTraiterBy($this->getUser()->getUsername());

            $this->demandeRepository->flush();
            $this->ficheRepository->flush();

            $this->addFlash('success', 'Les changements ont bien été appliqués');

            try {
                $this->mailerBottin->sendMailConfirmDemande($fiche);
                $this->addFlash('success', 'Un email de confirmation à bien été envoyé');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('danger', 'L\'envoie de confirmation par email à échoué : '.$e->getMessage());
            } catch (\Exception $e) {
                $this->addFlash('warning', 'L\'envoie de confirmation par email à échoué : '.$e->getMessage());
            }

            return $this->redirectToRoute('bottin_demande');
        }

        return $this->render(
            '@AcMarcheBottin/demande/show.html.twig',
            [
                'fiche' => $fiche,
                'demande' => $demande,
                'form' => $editForm->createView(),
                'properties' => $this->propertyUtil->getPropertyAccessor(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="bottin_demande_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Demande $demande): Response
    {
        if ($this->isCsrfTokenValid('delete'.$demande->getId(), $request->request->get('_token'))) {
            $this->demandeRepository->remove($demande);
            $this->demandeRepository->flush();
            $this->addFlash('success', 'Le demande a bien été supprimée');
        }

        return $this->redirectToRoute('bottin_demande');
    }
}
