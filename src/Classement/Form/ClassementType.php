<?php

namespace AcMarche\Bottin\Classement\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;

class ClassementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('name', SearchType::class, [
                'label' => 'Mot clef',
                'attr' => ['data-autocomplete-target' => 'input'],
                'help' => 'Rechercher et cliquez sur une des suggestions puis sur "Ajouter"',
                'required' => false,
            ])
            ->add(
                'categorySelected',
                HiddenType::class,
                [
                    'required' => true,
                    'attr' => ['data-classement-target' => 'selectedCategory', 'data-autocomplete-target' => 'hidden'],
                ]
            );
    }
}
