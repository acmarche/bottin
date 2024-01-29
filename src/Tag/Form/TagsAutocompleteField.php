<?php

namespace AcMarche\Bottin\Tag\Form;

use AcMarche\Bottin\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class TagsAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'class' => Tag::class,
            //  'filter_query' => function (QueryBuilder $qb, string $query, TagRepository $tagRepository) {
            //      $tagRepository->findByName($query);
            //  },
            'label' => 'Tags',
            'help' => 'Cliquez pour voir la liste des tags',
            'choice_label' => 'name',
            'multiple' => true,
            'tom_select_options' => [
                'create' => false,
                'createOnBlur' => false,
            ],
            'allow_options_create' => false,
            'constraints' => [
                // new Count(min: 1, minMessage: 'We need to eat *something*'),
            ],
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
