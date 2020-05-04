<?php

namespace AcMarche\Bottin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;

class ClassementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TelType::class,
                [
                    'label' => 'Mot clef',
                    'attr' => ['class' => 'typeahead', 'size' => '40px;', 'autocomplete' => 'off'],
                    'label_attr' => ['class' => 'mr-2'],
                    'mapped' => false,
                    'required' => false,
                ]
            )
            ->add(
                'categorySelected',
                HiddenType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'principal',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Classement principal',
                    'label_attr' => [
                        'class' => 'switch-custom',
                    ],
                ]
            );
    }
}
