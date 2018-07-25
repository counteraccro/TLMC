<?php
namespace App\Form;

use App\Entity\Patient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PatientType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom')
            ->add('prenom', TextType::class, array(
            'label' => 'Prénom'
        ))
            ->add('date_naissance', DateTimeType::class, array(
            'label' => 'Date de naissance',
            'widget' => 'choice',
            'years' => range(date('Y') - 100, date('Y')),
            'date_format' => 'dd MM yyyy'
        ))
            ->add('PMR', CheckboxType::class, array(
            'required' => false,
            'label' => 'Personne à mobilité réduite'
        ))
            ->add('familles', CollectionType::class, array(
            'label' => 'Formulaire d\'ajout d\'un nouveau membre de famille',
            'label_attr' => array(
                'id' => 'label_collection_type'
            ),
            'entry_type' => FamilleType::class,
            'entry_options' => array(
                'label' => '--- Ajouter un nouveau membre de famille ---',
                'cascade' => true
            ),

            'allow_add' => true,
            'auto_initialize' => true
        ))
            ->add('save', SubmitType::class, array(
            'label' => $options['label_submit'],
            'attr' => array(
                'class' => 'btn btn-primary'
            )
        ));
        // ->add('specialite')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Patient::class,
            'label_submit' => 'Valider'
        ]);
    }
}
