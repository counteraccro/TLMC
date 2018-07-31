<?php
namespace App\Form;

use App\Entity\Specialite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Etablissement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SpecialiteType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('etablissement', EntityType::class, array(
            'class' => Etablissement::class,
            'choice_label' => 'nom',
            'disabled' => $options['disabled_etablissement']
        ))
            ->add('service', TextType::class, array(
            'label' => 'Nom du service'
        ))
            ->add('code_logistique', TextType::class, array(
            'label' => 'Code logistique'
        ))
            ->
        add('adulte', CheckboxType::class, array(
            'label' => "Service pour adultes",
            'required' => false
        ))
            ->add('pediatrie', CheckboxType::class, array(
            'label' => "Service pour enfants",
            'required' => false
        ))
            ->
        add('save', SubmitType::class, array(
            'label' => $options['label_submit'],
            'attr' => array(
                'class' => 'btn btn-primary'
            )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Specialite::class,
            'label_submit' => 'Valider',
            'disabled_etablissement' => false
        ]);
    }
}
