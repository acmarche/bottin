<?php

namespace AcMarche\Bottin\Category\Form;

use AcMarche\Bottin\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Nom',
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 5],
                ]
            )
            ->add(
                'mobile',
                CheckboxType::class,
                [
                    'label' => 'Visible sur la version mobile ?',
                    'required' => false,
                ]
            )
            ->add(
                'logoFile',
                VichImageType::class,
                [
                    'required' => false,
                    'label' => 'Logo',
                    'help' => 'Uniquement utilisé sur Cap',
                ]
            )
            ->add(
                'logoBlancFile',
                VichImageType::class,
                [
                    'required' => false,
                    'label' => 'Logo blanc',
                    'help' => 'Uniquement utilisé sur Cap',
                ]
            )
            ->add(
                'iconFile',
                VichImageType::class,
                [
                    'required' => false,
                    'label' => 'Icône',
                    'help' => 'Utilisé sur la carto',
                    'constraints' => [],
                ]
            )
            ->add(
                'color',
                ColorType::class,
                [
                    'required' => false,
                    'label' => 'Couleur de l\'icône',
                    'help' => 'Utilisé sur la carto',
                ]
            );
        // ->add('parent');
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Category::class,
            ]
        );
    }
}
