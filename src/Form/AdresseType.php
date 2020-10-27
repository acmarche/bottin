<?php

namespace AcMarche\Bottin\Form;

use AcMarche\Bottin\Entity\Adresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => false,
                    'help' => 'Hdv, Place aux foires, Place x',
                ]
            )
            ->add(
                'rue',
                TextType::class,
                [
                    'required' => true,
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
                'cp',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'localite',
                TextType::class,
                [
                    'required' => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Adresse::class,
            ]
        );
    }
}
