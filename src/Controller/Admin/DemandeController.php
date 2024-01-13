<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Demande;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Form\DemandeType;
use AcMarche\Bottin\Mailer\MailFactory;
use AcMarche\Bottin\Repository\DemandeRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Utils\PropertyUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Demande controller.
 */
#[Route(path: '/admin/demande')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class DemandeController extends AbstractController
{
    public function __construct(
        private readonly DemandeRepository $demandeRepository,
        private readonly FicheRepository $ficheRepository,
        private readonly MailFactory $mailFactory,
        private readonly MailerInterface $mailer,
        private readonly PropertyUtil $propertyUtil
    ) {
    }

    /**
     * Lists all Demande entities.
     */
    #[Route(path: '/', name: 'bottin_admin_demande', methods: ['GET'])]
    public function index(): Response
    {
        $demandes = $this->demandeRepository->search();

        return $this->render(
            '@AcMarcheBottin/admin/demande/index.html.twig',
            [
                'demandes' => $demandes,
            ]
        );
    }

    /**
     * Finds and displays a Demande entity.
     */
    #[Route(path: '/{id}', name: 'bottin_admin_demande_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Demande $demande): RedirectResponse|Response|NotFoundHttpException
    {
        $fiche = $this->ficheRepository->find($demande->fiche);
        if (!$fiche instanceof Fiche) {
            return $this->createNotFoundException('Fiche non trouvée');
        }

        $editForm = $this->createForm(DemandeType::class, $demande);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if ($demande->traiter) {
                $this->addFlash('warning', 'Cette demande a déjà été traitée');

                return $this->redirectToRoute('bottin_admin_demande_show', ['id' => $demande->getId()]);
            }

            $metas = $request->request->all('metas');
            if (0 == \count($metas)) {
                $this->addFlash('danger', 'Il faut au moins un champ à modifier pour valider la demande');

                return $this->redirectToRoute('bottin_admin_demande_show', ['id' => $demande->getId()]);
            }

            foreach ($metas as $champ => $value) {
                $set = 'set'.ucfirst($champ);
                $fiche->$set($value);
            }

            $demande->traiter =true;
            $demande->traiter_by = $this->getUser()->getUserIdentifier();

            $this->demandeRepository->flush();
            $this->ficheRepository->flush();

            $this->addFlash('success', 'Les changements ont bien été appliqués');
            $email = $this->mailFactory->mailConfirmDemande($fiche);

            try {
                $this->mailer->send($email);
                $this->addFlash('success', 'Un email de confirmation à bien été envoyé');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('danger', 'L\'envoie de confirmation par email à échoué : '.$e->getMessage());
            } catch (\Exception $e) {
                $this->addFlash('warning', 'L\'envoie de confirmation par email à échoué : '.$e->getMessage());
            }

            return $this->redirectToRoute('bottin_admin_demande');
        }

        return $this->render(
            '@AcMarcheBottin/admin/demande/show.html.twig',
            [
                'fiche' => $fiche,
                'demande' => $demande,
                'form' => $editForm->createView(),
                'properties' => $this->propertyUtil->getPropertyAccessor(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'bottin_admin_demande_delete', methods: ['POST'])]
    public function delete(Request $request, Demande $demande): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$demande->getId(), $request->request->get('_token'))) {
            $this->demandeRepository->remove($demande);
            $this->demandeRepository->flush();
            $this->addFlash('success', 'Le demande a bien été supprimée');
        }

        return $this->redirectToRoute('bottin_admin_demande');
    }
}
