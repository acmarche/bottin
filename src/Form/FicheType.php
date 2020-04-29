<?php

namespace AcMarche\Bottin\Form;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\Pdv;
use AcMarche\Bottin\Repository\PdvRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FicheType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('societe', TextType::class)
            ->add(
                'rue',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'numero',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'cp',
                IntegerType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'localite',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'telephone',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'telephone_autre',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'fax',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'gsm',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'website',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'facebook',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'twitter',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'instagram',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'centreville',
                CheckboxType::class,
                [
                    'required' => false,
                    'label_attr' => [
                        'class' => 'switch-custom',
                    ],
                ]
            )
            ->add(
                'midi',
                CheckboxType::class,
                [
                    'required' => false,
                    'label_attr' => [
                        'class' => 'switch-custom',
                    ],
                ]
            )
            ->add(
                'pmr',
                CheckboxType::class,
                [
                    'required' => false,
                    'label_attr' => [
                        'class' => 'switch-custom',
                    ],
                ]
            )
            ->add(
                'fonction',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'civilite',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'prenom',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_rue',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_num',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_cp',
                IntegerType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_localite',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_telephone',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_telephone_autre',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_fax',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_gsm',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'contact_email',
                EmailType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'admin_fonction',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'admin_civilite',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'admin_nom',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'admin_prenom',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'admin_telephone',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'admin_telephone_autre',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'admin_fax',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'admin_gsm',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'admin_email',
                EmailType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'comment1',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 6],
                ]
            )
            ->add(
                'comment2',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 6],
                ]
            )
            ->add(
                'comment3',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 6],
                ]
            )
            ->add(
                'note',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'Note (privé)',
                    'help'=>'Cette information n\'apparaitra pas pour le publique',
                    'attr' => ['rows' => 3],
                ]
            )
            ->add(
                'pdv',
                EntityType::class,
                [
                    'required' => false,
                    'class' => Pdv::class,
                    'query_builder' => function (PdvRepository $cr) {
                        return $cr->getForList();
                    },
                ]
            );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $fiche = $event->getData();
                $form = $event->getForm();

                if (null != $fiche->getId()) {
                    $form->add(
                        'horaires',
                        CollectionType::class,
                        [
                            'entry_type' => HoraireType::class,
                        ]
                    );
                }
            }
        );
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
