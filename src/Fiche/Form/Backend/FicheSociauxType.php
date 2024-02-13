<?php

namespace AcMarche\Bottin\Fiche\Form\Backend;

use AcMarche\Bottin\Entity\Fiche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FicheSociauxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'facebook',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'twitter',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'instagram',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'tiktok',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'youtube',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'linkedin',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
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
