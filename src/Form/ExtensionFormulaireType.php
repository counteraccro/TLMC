<?php
namespace App\Form;

use App\Entity\ExtensionFormulaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ExtensionFormulaireType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('libelle', TextType::class, array(
            'label' => 'Libellé du champ'
        ))->add('valeur');
        if (! $options['add']) {
            // $builder->add('ordre', IntegerType::class);
            $builder->add('disabled', ChoiceType::class, array(
                'label' => 'Actif',
                'choices' => array(
                    'Oui' => 0,
                    'Non' => 1
                )
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ExtensionFormulaire::class,
            'add' => false
        ));
    }
}
