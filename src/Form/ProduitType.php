<?php
namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Controller\ProduitController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use App\Controller\AppController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProduitType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', ChoiceType::class, array(
            'choices' => array_flip($options['type'])
        ))
            ->add('titre')
            ->add('texte', TextareaType::class, array(
            'label' => 'Description'
        ))
            ->add('tranche_age', ChoiceType::class, array(
            'label' => "Tranche d'âge",
            'choices' => array_flip($options['trancheAge']),
            'multiple' => true
        ))
            ->add('genre', ChoiceType::class, array(
            'choices' => array_flip($options['genre'])
        ))
            ->add('texte_2', TextareaType::class, array(
            'label' => 'Informations complémentaires',
            'required' => false
        ))
            ->add('quantite', IntegerType::class, array(
            'label' => 'Quantité'
        ))
            ->add('date_envoi', DateTimeType::class, array(
            'label' => "Date d'envoi",
            'widget' => 'choice',
            'date_format' => 'dd MM yyyy',
            'years' => range(date('Y'), date('Y') + 2)
        ));

        if ($options['add_etablissement']) {
            $builder->add('produitEtablissements', CollectionType::class, array(
                'label' => "Etablissements dans lesquels le produit est envoyé",
                'label_attr' => array(
                    'id' => 'label_collection_type'
                ),
                'entry_type' => ProduitEtablissementType::class,
                'entry_options' => array(
                    'label' => ' ',
                    'avec_produit' => false,
                    'avec_bouton' => false
                ),

                'allow_add' => true,
                'auto_initialize' => true
            ));
        }

        if ($options['add_specialite']) {
            $builder->add('produitSpecialites', CollectionType::class, array(
                'label' => "Spécialités dans lesquelles le produit est envoyé",
                'label_attr' => array(
                    'id' => 'label_collection_type'
                ),
                'entry_type' => ProduitSpecialiteType::class,
                'entry_options' => array(
                    'label' => ' ',
                    'avec_produit' => false,
                    'avec_bouton' => false
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
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'label_submit' => 'Valider',
            'genre' => ProduitController::GENRE,
            'type' => ProduitController::TYPE,
            'trancheAge' => AppController::TRANCHE_AGE,
            'add_specialite' => FALSE,
            'add_etablissement' => false
        ]);
    }
}
