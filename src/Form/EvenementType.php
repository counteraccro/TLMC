<?php
namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use App\Controller\EvenementController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Controller\AppController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EvenementType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom')
            ->add('date_debut', DateTimeType::class, array(
            'label' => "Date de début",
            'widget' => 'choice',
            'years' => range(date('Y') - 1, date('Y') + 10)
        ))
            ->add('date_fin', DateTimeType::class, array(
            'label' => "Date de fin ",
            'widget' => 'choice',
            'years' => range(date('Y') - 1, date('Y') + 10)
        ))
            ->add('nombre_max', IntegerType::class, array(
            'label' => 'Nombre maximum de participant'
        ))
            ->add('description', TextareaType::class, array(
            'required' => false
        ))
            ->add('type', ChoiceType::class, array(
            'choices' => array_flip($options['type'])
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
            'years' => range(date('Y'), date('Y') + 2)
        ));

        if ($options['add']) {
            $builder->add('specialite_evenements', CollectionType::class, array(
                'label' => 'Spécialités',
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
        // ->add('image')
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Evenement::class,
            'label_submit' => 'Valider',
            'add' => false,
            'statut' => EvenementController::STATUT,
            'type' => EvenementController::TYPE,
            'trancheAge' => AppController::TRANCHE_AGE,
            'query_specialite' => null
        ));
    }
}
