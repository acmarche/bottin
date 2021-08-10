<?php

namespace AcMarche\Bottin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class ClassementSimpleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
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
