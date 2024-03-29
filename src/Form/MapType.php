<?php

namespace AcMarche\Bottin\Form;

use AcMarche\Bottin\Entity\Fiche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'adresse',
                TextType::class,
                [
                    'required' => false,
                    'help' => 'Si la fiche ne dipose pas d\'adresse, rue du commerce est mis par défaut le temps de géolocaliser',
                    'attr' => ['readonly' => true],
                ]
            )
            ->add('latitude')
            ->add('longitude');
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
