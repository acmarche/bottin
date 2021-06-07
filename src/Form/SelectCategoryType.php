<?php

namespace AcMarche\Bottin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SelectCategoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
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
                TextType::class,
                [
                    'required' => true,
                ]
            );
    }
}
