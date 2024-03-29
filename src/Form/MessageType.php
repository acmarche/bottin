<?php

namespace AcMarche\Bottin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'from',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'subject',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Sujet',
                ]
            )
            ->add(
                'message',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 5],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([]);
    }
}
