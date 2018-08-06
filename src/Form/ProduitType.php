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
            ->add('tranche_age', TextareaType::class, array(
            'label' => "Tranche d'âge"
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
            'years' => range(date('Y'), date('Y') + 2)
        ))
            ->add('save', SubmitType::class, array(
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
            'type' => ProduitController::TYPE
        ]);
    }
}
