<?php

namespace AcMarche\Bottin\Form\Search;

use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Utils\SortUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchCategoryType extends AbstractType
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $categories = [];
        $roots = $this->categoryRepository->getRootNodes();
        $roots = SortUtils::sortCategories($roots);
        foreach ($roots as $root) {
            $categories[$root->getName()] = $root->getId();
        }

        $builder
            ->add(
                'parent',
                ChoiceType::class,
                [
                    'required' => false,
                    'choices' => $categories,
                    'placeholder' => 'Racine',
                ]
            )
            ->add(
                'name',
                SearchType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Nom',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
