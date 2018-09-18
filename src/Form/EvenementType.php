<?php
namespace App\Form;

use App\Entity\Evenement;
use App\Controller\EvenementController;
use App\Controller\AppController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\TypeEvenement;
use App\Repository\TypeEvenementRepository;

class EvenementType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom')
            ->add('date_debut', DateTimeType::class, array(
            'label' => "Date de début",
            'widget' => 'choice',
            'date_format' => 'dd MM yyyy'
        ))
            ->add('date_fin', DateTimeType::class, array(
            'label' => "Date de fin ",
            'widget' => 'choice',
            'date_format' => 'dd MM yyyy'
        ))
            ->add('nombre_max', IntegerType::class, array(
            'label' => 'Nombre maximum de participant'
        ))
            ->add('description', TextareaType::class, array(
            'required' => false
        ))
            ->add('type', EntityType::class, array(
            'class' => TypeEvenement::class,
            'query_builder' => function (TypeEvenementRepository $ter) {
                return $ter->createQueryBuilder('te')
                        ->andWhere('te.disabled = 0')
                        ->orderBy('te.nom');
            },
            'choice_label' => 'nom'
        ))
            ->add('nom_lieu', TextType::class, array(
            'label' => 'Lieu'
        ))
            ->add('numero_voie', IntegerType::class, array(
            'label' => 'N° de la voie'
        ))
            ->add('voie')
            ->add('ville')
            ->add('code_postal')
            ->add('tranche_age', ChoiceType::class, array(
            'label' => "Tranche d'âge",
            'choices' => array_flip($options['trancheAge']),
            'multiple' => true
        ))
            ->add('information_complementaire', TextareaType::class, array(
            'label' => "Information complémentaire",
            'required' => false
        ))
            ->add('statut', ChoiceType::class, array(
            'choices' => array_flip($options['statut'])
        ))
            ->add('date_fin_inscription', DateTimeType::class, array(
            'label' => "Date de fin d'inscription",
            'widget' => 'choice',
            'date_format' => 'dd MM yyyy'
        ));

        if (! $options['ajax']) {
            $builder->add('image_1', FileType::class, array(
                'label' => 'Image 1',
                'data_class' => null,
                'required' => false,
                'help' => ($options['add'] ? $options['aide_ajout'] : $options['aide_edition']),
                'attr' => array(
                    'placeholder' => 'Choisir la première image'
                )
            ));

            $builder->add('image_2', FileType::class, array(
                'label' => 'Image 2',
                'data_class' => null,
                'required' => false,
                'help' => ($options['add'] ? $options['aide_ajout'] : $options['aide_edition']),
                'attr' => array(
                    'placeholder' => 'Choisir la seconde image'
                )
            ));

            $builder->add('image_3', FileType::class, array(
                'label' => 'Image 3',
                'data_class' => null,
                'required' => false,
                'help' => ($options['add'] ? $options['aide_ajout'] : $options['aide_edition']),
                'attr' => array(
                    'placeholder' => 'Choisir la troisième image'
                )
            ));
        }

        $builder->add('extension_formulaires', CollectionType::class, array(
            'label' => 'Champs supplémentaires',
            'label_attr' => array(
                'id' => 'label_collection_type'
            ),
            'entry_type' => ExtensionFormulaireType::class,
            'entry_options' => array(
                'label' => ' ',
                'add' => $options['add']
            ),

            'allow_add' => true,
            'auto_initialize' => true
        ));

        if ($options['add']) {
            $builder->add('specialite_evenements', CollectionType::class, array(
                'label' => "Spécialités auxquelles l'événement est proposé",
                'label_attr' => array(
                    'id' => 'label_collection_type'
                ),
                'entry_type' => SpecialiteEvenementType::class,
                'entry_options' => array(
                    'label' => ' ',
                    'avec_event' => false,
                    'avec_bouton' => false,
                    'query_specialite' => $options['query_specialite']
                ),

                'allow_add' => true,
                'auto_initialize' => true
            ));
        }
        $builder->add('save', SubmitType::class, array(
            'label' => $options['label_submit'],
            'attr' => array(
                'class' => 'btn btn-primary'
            )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Evenement::class,
            'label_submit' => 'Valider',
            'add' => false,
            'statut' => EvenementController::STATUT,
            'trancheAge' => AppController::TRANCHE_AGE,
            'query_specialite' => null,
            'ajax' => false,
            'allow_extra_fields' => true,
            'aide_ajout' => 'Formats de fichier acceptés : ' . implode(', ', AppController::FORMAT_IMAGE),
            'aide_edition' => 'Ne pas remplir si vous souhaitez conserver la même image'
        ));
    }
}
