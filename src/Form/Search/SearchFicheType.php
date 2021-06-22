<?php

namespace AcMarche\Bottin\Form\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFicheType extends AbstractType
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
                SearchType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Mot clef',
                        'autocomplete' => 'off',
                    ],
                ]
            )
            ->add(
                'localite',
                SearchType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Localité',
                        'autocomplete' => 'off',
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
