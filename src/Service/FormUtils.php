<?php

namespace AcMarche\Bottin\Service;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Form\Fiche\FicheActiviteType;
use AcMarche\Bottin\Form\Fiche\FicheComplementType;
use AcMarche\Bottin\Form\Fiche\FicheContactType;
use AcMarche\Bottin\Form\Fiche\FicheSociauxType;
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
