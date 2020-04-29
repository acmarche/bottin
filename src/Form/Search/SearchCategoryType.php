<?php

namespace AcMarche\Bottin\Form\Search;

use AcMarche\Bottin\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $categories = [];
        $roots = $this->categoryRepository->getRootNodes();
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
