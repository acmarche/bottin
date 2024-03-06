<?php

namespace AcMarche\Bottin\Tag\Form;

use AcMarche\Bottin\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Vich\UploaderBundle\Form\Type\VichImageType;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'color',
                ColorType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'private',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Rendre ce tag privé',
                    'help' => 'Ce tag n\'apparaîtra pas sur la carte',
                ]
            )
            ->add(
                'iconFile',
                VichImageType::class,
                [
                    'label' => 'Icône',
                    'required' => false,
                    'help_html' => true,
                    'help' => 'Vous pouvez en trouvez sur https://tabler.io/icons, https://icon-sets.iconify.design, https://thenounproject.com/search/icons',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Tag::class,
            ]
        );
    }
}
