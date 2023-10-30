<?php

namespace AcMarche\Bottin\Meta\Form;

use AcMarche\Bottin\Entity\MetaData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MetaDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'value',
                TextType::class,
                [
                    'label' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => MetaData::class,
            ]
        );
    }

}