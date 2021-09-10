<?php

namespace AcMarche\Bottin\Fiche\Form;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Horaire\Form\HoraireType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FicheHoraireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'horaires',
                CollectionType::class,
                [
                    'entry_type' => HoraireType::class,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Fiche::class,
            ]
        );
    }
}
