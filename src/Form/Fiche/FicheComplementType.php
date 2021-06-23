<?php

namespace AcMarche\Bottin\Form\Fiche;

use AcMarche\Bottin\Entity\Fiche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FicheComplementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'midi',
                CheckboxType::class,
                [
                    'required' => false,
                    'label_attr' => [
                        'class' => 'switch-custom',
                    ],
                ]
            )
            ->add(
                'pmr',
                CheckboxType::class,
                [
                    'required' => false,
                    'label_attr' => [
                        'class' => 'switch-custom',
                    ],
                ]
            )
            ->add(
                'comment1',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 6],
                ]
            )
            ->add(
                'comment2',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 6],
                ]
            )
            ->add(
                'comment3',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 6],
                ]
            );
    }

    /**
     * @param OptionsResolver $optionsResolver
     */
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Fiche::class,
            ]
        );
    }
}
