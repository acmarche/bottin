<?php

namespace AcMarche\Bottin\Category\Form;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryMoveType extends AbstractType
{
    public function __construct(private CategoryRepository $categoryRepository)
    {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'parent',
                EntityType::class,
                [
                    'class' => Category::class,
                    'choice_label' => 'labelHierarchical',
                    // 'query_builder'=>$this->categoryRepository->getQb(),
                    'choices' => $this->categoryRepository->getAllTree(),
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Category::class,
            ]
        );
    }
}
