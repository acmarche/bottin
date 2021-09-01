<?php

namespace AcMarche\Bottin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('nom')
            ->add('email', EmailType::class)
            ->add(
                'message',
                TextareaType::class,
                [
                    'attr' => ['rows' => 5],
                ]
            );
    }
}
