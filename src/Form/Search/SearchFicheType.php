<?php

namespace AcMarche\Bottin\Form\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFicheType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
