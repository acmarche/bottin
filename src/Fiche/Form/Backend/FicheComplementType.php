<?php

namespace AcMarche\Bottin\Fiche\Form\Backend;

use AcMarche\Bottin\Entity\Fiche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FicheComplementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'midi',
                CheckboxType::class,
                [
                    'label' => 'Ouvert sur le temps de midi',
                    'required' => false,
                    'label_attr' => [
                        'class' => 'checkbox-switch',
                    ],
                ]
            )
            ->add(
                'pmr',
                CheckboxType::class,
                [
                    'required' => false,
                    'label_attr' => [
                        'class' => 'checkbox-switch',
                    ],
                    'label' => 'Accessible aux personnes à mobilités réduites',
                ]
            )
            ->add(
                'comment1',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 6],
                    'help' => 'Description de votre activité',
                ]
            )
            ->add(
                'comment2',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 6],
                    'help' => 'Horaires',
                ]
            )
            ->add(
                'comment3',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 6],
                    'help' => "D'autres informations",
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
