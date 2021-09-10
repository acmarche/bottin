<?php

namespace AcMarche\Bottin\Location\Form;

use AcMarche\Bottin\Location\LocationAbleInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalisationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'latitude',
                TextType::class,
                [
                    'attr' => ['placeholder' => 'latitude'],
                ]
            )
            ->add(
                'longitude',
                TextType::class,
                [
                    'attr' => ['placeholder' => 'longitude'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            ['data_class' => LocationAbleInterface::class]
        );
    }
}
