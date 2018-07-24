<?php

namespace App\Form;

use App\Entity\FamilleAdresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FamilleAdresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('numero_voie', TextType::class, array('label' => 'Numéro de voie'))
            ->add('voie')
            ->add('ville')
            ->add('code_postal')
//             ->add('disabled', CheckboxType::class, array(
//                 'required' => false,
//                 'label' => 'Actif'
//             ))
            ->add('save', SubmitType::class, array(
                'label' => 'Valider'
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
