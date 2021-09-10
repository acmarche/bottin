<?php

namespace AcMarche\Bottin\Fiche\Form\Backend;

use AcMarche\Bottin\Entity\Fiche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FicheContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'fonction',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'civilite',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'prenom',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_rue',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_num',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_cp',
                IntegerType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_localite',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_telephone',
                TelType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_telephone_autre',
                TelType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_fax',
                TelType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_gsm',
                TelType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_email',
                EmailType::class,
                [
                    'required' => false,
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
