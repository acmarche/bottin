<?php

namespace AcMarche\Bottin\Form;

use AcMarche\Bottin\Entity\Fiche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'adresse',
                TextType::class,
                [
                    'required' => false,
                    'help' => 'Si la fiche ne dipose pas d\'adresse, rue du commerce est mis par défaut le temps de géolocaliser',
                    'attr' => ['readonly' => true],
                ]
            )
            ->add('latitude')
            ->add('longitude');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Fiche::class,
            ]
        );
    }
}
