<?php

namespace AcMarche\Bottin\Form\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchHistoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'nom',
                SearchType::class,
                [
                    'required' => false,
                    'label' => 'Fiche',
                    'attr' => [
                        'autocomplete' => 'off',
                    ],
                ]
            )
            ->add(
                'madeBy',
                SearchType::class,
                [
                    'required' => false,
                    'label' => 'Fait par',
                    'help' => 'Tapez "token" pour les particuliers',
                    'attr' => [
                        'autocomplete' => 'off',
                    ],
                ]
            )
            ->add(
                'property',
                ChoiceType::class,
                [
                    'choices' => array_combine($this->fields(), $this->fields()),
                    'required' => false,
                    'label' => 'Quel champ',
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([]);
    }

    private function fields(): array
    {
        return [
            'Societe',
            'suppression de fiche',
            'nouvelle fiche',
            'new',
            'image',
            'classement',
            'nom',
            'nom',
            'prenom',
            'civilite',
            'fonction',
            'rue',
            'numero',
            'cp',
            'localite',
            'telephone',
            'telephone_autre',
            'gsm',
            'fax',
            'email',
            'site',
            'centre_ville',
            'midi',
            'pmr',
            'ecommerce',
            'ClickAndCollect',
            'pdv',
            'Contact_nom',
            'Contact_prenom',
            'Contact_fonction',
            'Contact_rue',
            'Contact_num',
            'Contact_cp',
            'Contact_tocalite',
            'Contact_telephone',
            'Contact_telephone_autre',
            'Contact_gsm',
            'Contact_fax',
            'Contact_email',
            'facebook',
            'twitter',
            'Instagram',
            'Comment1',
            'Comment2',
            'Comment3',
            'Classements',
            'numero_tva',
        ];
    }
}
