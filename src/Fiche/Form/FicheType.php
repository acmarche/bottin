<?php

namespace AcMarche\Bottin\Fiche\Form;

use AcMarche\Bottin\Entity\Adresse;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\Pdv;
use AcMarche\Bottin\Entity\Situation;
use AcMarche\Bottin\Fiche\Form\Backend\AddFieldsMetaSubscriber;
use AcMarche\Bottin\Horaire\Form\HoraireType;
use AcMarche\Bottin\Meta\Form\MetaDataType;
use AcMarche\Bottin\Meta\Repository\MetaFieldRepository;
use AcMarche\Bottin\Repository\AdresseRepository;
use AcMarche\Bottin\Repository\PdvRepository;
use AcMarche\Bottin\Tag\Form\TagsAutocompleteField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FicheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('societe', TextType::class)
            ->add(
                'adresse',
                EntityType::class,
                [
                    'class' => Adresse::class,
                    'query_builder' => static fn(AdresseRepository $adresseRepository
                    ) => $adresseRepository->queryBuilderForSelect(),
                    'required' => false,
                    'placeholder' => 'Sélectionnez une adresse existante',
                    'help' => 'Cette adresse écrasera l\' adresse encodée sur la fiche ',
                ]
            )
            ->add(
                'rue',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'numero',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'cp',
                IntegerType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
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
                TelType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'telephone_autre',
                TelType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'fax',
                TelType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'gsm',
                TelType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'website',
                UrlType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'numeroTva',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Numéro de Tva',
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'facebook',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'twitter',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'instagram',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'tiktok',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'situations',
                EntityType::class,
                [
                    'class' => Situation::class,
                    'multiple' => true,
                    'expanded' => true,
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
                    'attr' => ['autocomplete' => 'off'],
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
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'prenom',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'contact_rue',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'contact_num',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'contact_cp',
                IntegerType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'contact_localite',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'contact_telephone',
                TelType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'contact_telephone_autre',
                TelType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'contact_fax',
                TelType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'contact_gsm',
                TelType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
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
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'admin_civilite',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'admin_nom',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'admin_prenom',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'admin_telephone',
                TelType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'admin_telephone_autre',
                TelType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'admin_fax',
                TelType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'admin_gsm',
                TelType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'admin_email',
                EmailType::class,
                [
                    'required' => false,
                    'attr' => ['autocomplete' => 'off'],
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
                    'help' => "Cette information n'apparaitra pas pour le public",
                    'attr' => ['rows' => 3],
                ]
            )
            ->add(
                'pdv',
                EntityType::class,
                [
                    'required' => false,
                    'class' => Pdv::class,
                    'query_builder' => static fn(PdvRepository $cr) => $cr->getForList(),
                ]
            )
            ->add('tags', TagsAutocompleteField::class)
            /*->add('metas', CollectionType::class, [
                'entry_type' => MetaDataType::class,
                'entry_options' => ['label' => false],
            ])*/;
        // ->addEventSubscriber(new AddFieldsMetaSubscriber($this->metaFieldRepository));

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            static function (FormEvent $event) {
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Fiche::class,
            ]
        );
    }
}
