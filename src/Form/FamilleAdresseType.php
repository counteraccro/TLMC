<?php

namespace App\Form;

use App\Entity\FamilleAdresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class FamilleAdresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero_voie')
            ->add('voie')
            ->add('ville')
            ->add('code_postal')
            ->add('disabled', CheckboxType::class, array(
                'required' => false,
                'label' => 'Actif'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FamilleAdresse::class,
        ]);
    }
}
