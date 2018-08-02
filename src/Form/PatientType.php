<?php
namespace App\Form;

use App\Entity\Patient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Specialite;

class PatientType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $specialite_options = array(
            'class' => Specialite::class,
            'label' => 'Spécialité',
            'choice_label' => 'service'
        );
        if (count($options['specialites'])) {
            $specialite_options['choices'] = $options['specialites'];
        }

        $builder->add('nom')
            ->add('prenom', TextType::class, array(
            'label' => 'Prénom'
        ))
            ->add('date_naissance', BirthdayType::class, array(
            'label' => 'Date de naissance'
        ))
            ->add('PMR', CheckboxType::class, array(
            'required' => false,
            'label' => 'Personne à mobilité réduite'
        ))
        ->add('specialite', EntityType::class, $specialite_options);
        
        if ($options['add']) {
            $builder->add('familles', CollectionType::class, array(
                'label' => 'Formulaire d\'ajout d\'un nouveau membre de famille',
                'label_attr' => array(
                    'id' => 'label_collection_type'
                ),
                'entry_type' => FamilleType::class,
                'entry_options' => array(
                    'label' => '--- Ajouter un nouveau membre de famille ---',
                    'avec_bouton' => false,
                    'avec_patient' => false
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
            'data_class' => Patient::class,
            'label_submit' => 'Valider',
            'allow_extra_fields' => true,
            'add' => true,
            'specialites' => array()
        ));
    }
}
