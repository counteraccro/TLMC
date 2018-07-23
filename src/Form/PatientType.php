<?php
namespace App\Form;

use App\Entity\Patient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

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
            ->
        add('PMR', CheckboxType::class, array(
            'required' => false,
            'label' => 'Personne à mobilité réduite'
        ))
            ->add('disabled', CheckboxType::class, array(
            'required' => false,
            'label' => 'Actif'
        ))
        // ->add('specialite')
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Patient::class
        ]);
    }
}
