<?php

namespace AcMarche\Bottin\Form;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryMoveType extends AbstractType
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
        $builder
            ->add(
                'parent',
                EntityType::class,
                [
                    'class' => Category::class,
                    'choice_label' => 'labelHierarchical',
                    // 'query_builder'=>$this->categoryRepository->getQb(),
                    'choices' => $this->categoryRepository->getAllTree()
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Category::class,
            ]
        );
    }
}
