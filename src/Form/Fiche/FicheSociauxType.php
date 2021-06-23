<?php

namespace AcMarche\Bottin\Form\Fiche;

use AcMarche\Bottin\Entity\Fiche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FicheSociauxType extends AbstractType
{
    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'facebook',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'twitter',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'instagram',
                TextType::class,
                [
                    'required' => false,
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
