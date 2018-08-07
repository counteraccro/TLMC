<?php

namespace App\Form;

use App\Entity\ProduitSpecialite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Specialite;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProduitSpecialiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['avec_specialite']) {
            $builder->add('specialite', EntityType::class, array(
                'class' => Specialite::class,
                'choice_label' => 'nom',
                'disabled' => $options['disabled_specialite']
            ));
        }
        if ($options['avec_produit']) {
            $builder->add('produit', EntityType::class, array(
                'class' => Produit::class,
                'choice_label' => 'titre',
                'disabled' => $options['disabled_produit']
            ));
        }
        $builder->add('quantite', IntegerType::class, array(
            'label' => 'Quantité'
        ))->add('date', DateTimeType::class, array(
            'label' => "Date",
            'widget' => 'choice',
            'years' => range(date('Y') - 1, date('Y') + 2)
        ));
        
        if ($options['avec_bouton']) {
            $builder->add('save', SubmitType::class, array(
                'label' => $options['label_submit'],
                'attr' => array(
                    'class' => 'btn btn-primary'
                )
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProduitSpecialite::class,
            'label_submit' => 'Valider',
            'avec_bouton' => true,
            'avec_specialite' => true,
            'avec_produit' => true,
            'disabled_specialite' => false,
            'disabled_produit' => false
        ]);
    }
}
