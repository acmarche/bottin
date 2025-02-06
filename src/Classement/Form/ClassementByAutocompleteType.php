<?php

namespace AcMarche\Bottin\Classement\Form;

use AcMarche\Bottin\Category\Form\CategoryAutocompleteField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class ClassementByAutocompleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('categories', CategoryAutocompleteField::class)
            ->add(
                'categorySelected',
                HiddenType::class,
                [
                    'required' => true,
                    'attr' => ['data-classement-target' => 'selectedCategory'],
                ]
            );
    }
}
