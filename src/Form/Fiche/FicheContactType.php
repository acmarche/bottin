<?php

namespace AcMarche\Bottin\Form\Fiche;

use AcMarche\Bottin\Entity\Fiche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FicheContactType extends AbstractType
{
    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     */
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
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_telephone_autre',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_fax',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_gsm',
                TextType::class,
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
