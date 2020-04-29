<?php

namespace AcMarche\Bottin\Form\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        $types = ['match', 'matchPhrase', 'multiMatch', 'bool', 'all', 'filter'];
        $builder
            ->add(
                'nom',
                SearchType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Mot clef',
                        'autocomplete' => 'off'
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
                        'autocomplete' => 'off'
                    ],
                ]
            )
            ->add(
                'type',
                ChoiceType::class,
                [
                    'choices' => array_combine($types, $types),
                    'required' => true,
                ]
            )
            ->add(
                'raz',
                SubmitType::class,
                [
                    'label' => 'Raz',
                    'attr' => [
                        'class' => 'btn-sm btn-success',
                        'title' => 'Raz search',
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
