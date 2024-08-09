<?php

namespace AcMarche\Bottin\Category\Form;

use AcMarche\Bottin\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class CategoryAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Category::class,
            'searchable_fields' => ['name'],
            'label' => 'Secteur(s)',
            'choice_label' => 'name',
            'multiple' => true,
            'constraints' => [
                new Count(min: 1, minMessage: 'Il faut au moins un secteur'),
            ],
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
