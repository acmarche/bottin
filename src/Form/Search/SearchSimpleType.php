<?php

namespace AcMarche\Bottin\Form\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchSimpleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => true,
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Rechercher...',
                        'autocomplete' => 'off',
                        'data-autocomplete-target' => 'input',
                    ],
                ]
            );
    }

    /**
     * @param OptionsResolver $optionsResolver
     */
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([]);
    }
}
