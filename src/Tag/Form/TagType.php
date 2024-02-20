<?php

namespace AcMarche\Bottin\Tag\Form;

use AcMarche\Bottin\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'color',
                ColorType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'icon',
                TextType::class,
                [
                    'label' => 'Icone',
                    'attr' => ['placeholder' => 'ti ti-food'],
                    'required' => false,
                    'help_html' => true,
                    'help' => 'Format: ti ti-xxx. Liste des balises est disponible sur <a href="https://tabler.io/icons" target="_blank">Tabler.io</a>',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Tag::class,
            ]
        );
    }
}
