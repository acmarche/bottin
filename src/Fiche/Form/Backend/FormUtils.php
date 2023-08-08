<?php

namespace AcMarche\Bottin\Fiche\Form\Backend;

use AcMarche\Bottin\Entity\Fiche;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class FormUtils
{
    public function __construct(private readonly FormFactoryInterface $formFactory)
    {
    }

    public function createFormByEtape(Fiche $fiche): FormInterface
    {
        $etape = $fiche->getEtape();

        return match ($etape) {
            2 => $this->formFactory->create(FicheContactType::class, $fiche),
            3 => $this->formFactory->create(FicheSociauxType::class, $fiche),
            4 => $this->formFactory->create(FicheComplementType::class, $fiche),
            default => $this->formFactory->create(FicheActiviteType::class, $fiche),
        };
    }
}
