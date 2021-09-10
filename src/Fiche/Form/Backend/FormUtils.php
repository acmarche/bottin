<?php

namespace AcMarche\Bottin\Fiche\Form\Backend;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Fiche\Form\Backend\FicheActiviteType;
use AcMarche\Bottin\Fiche\Form\Backend\FicheComplementType;
use AcMarche\Bottin\Fiche\Form\Backend\FicheContactType;
use AcMarche\Bottin\Fiche\Form\Backend\FicheSociauxType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class FormUtils
{
    private FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function createFormByEtape(Fiche $fiche): FormInterface
    {
        $etape = $fiche->getEtape();
        switch ($etape) {
            case 2:
                return $this->formFactory->create(FicheContactType::class, $fiche);
            case 3:
                return $this->formFactory->create(FicheSociauxType::class, $fiche);
            case 4:
                return $this->formFactory->create(FicheComplementType::class, $fiche);
            default:
                return $this->formFactory->create(FicheActiviteType::class, $fiche);
        }
    }
}
