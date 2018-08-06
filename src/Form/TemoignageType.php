<?php
namespace App\Form;

use App\Entity\Temoignage;
use App\Entity\Evenement;
use App\Entity\Produit;
use App\Controller\AppController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TemoignageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titre')
            ->add('corps', TextareaType::class)
            ->add('prenom_temoin', TextType::class, array(
            'label' => 'Prénom du témoin'
        ))
            ->add('age', NumberType::class)
            ->add('lien_parente', ChoiceType::class, array(
            'label' => 'Lien de parenté avec le patient',
            'choices' => array_flip($options['famille_parente'])
        ))
            ->add('ville');
        if ($options['avec_event']) {
            $builder->add('evenement', EntityType::class, array(
                'class' => Evenement::class,
                'disabled' => $options['disabled_event'],
                'choice_label' => 'nom',
                'required' => false
            ));
        }
        if ($options['avec_prod']) {
            $builder->add('produit', EntityType::class, array(
                'class' => Produit::class,
                'disabled' => $options['disabled_prod'],
                'choice_label' => 'titre',
                'required' => false
            ));
        }
        $builder->add('save', SubmitType::class, array(
            'label' => $options['label_submit'],
            'attr' => array(
                'class' => 'btn btn-primary'
            )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Temoignage::class,
            'label_submit' => 'Valider',
            'famille_parente' => AppController::FAMILLE_PARENTE,
            'disabled_event' => false,
            'disabled_prod' => false,
            'avec_event' => true,
            'avec_prod' => true
        ]);
    }
}
