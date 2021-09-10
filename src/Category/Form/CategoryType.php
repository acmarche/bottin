<?php

namespace AcMarche\Bottin\Category\Form;

use AcMarche\Bottin\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
                ]
            )
            ->add(
                'logoBlancFile',
                VichImageType::class,
                [
                    'required' => false,
                    'label' => 'Logo blanc',
                ]
            );
        //->add('parent');
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
