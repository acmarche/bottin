<?php

namespace AcMarche\Bottin\Form\Fiche;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Service\Bottin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FicheActiviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $localites = array_combine(Bottin::LOCALITES, Bottin::LOCALITES);
        $formBuilder
            ->add('societe', TextType::class)
            ->add(
                'rue',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'numero',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'localite',
                ChoiceType::class,
                [
                    'choices' => $localites,
                    'required' => false,
                ]
            )
            ->add(
                'telephone',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'telephone_autre',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'fax',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'gsm',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'website',
                UrlType::class,
                [
                    'required' => false,
                    'label' => 'Site internet',
                    'help' => 'Ex: https://www.monsite.be',
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'numeroTva',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Numéro de Tva',
                ]
            );

        $formBuilder->addEventSubscriber(new AddFieldEtapeSubscriber());
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
