<?php
namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProduitType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type')
            ->add('titre')
            ->add('texte', TextareaType::class, array(
            'label' => 'Description'
        ))
            ->add('tranche_age', TextType::class, array(
            'label' => "Tranche d'âge"
        ))
            ->add('genre', IntegerType::class)
            ->add('texte_2')
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
            'label_submit' => 'Valider'
        ]);
    }
}
