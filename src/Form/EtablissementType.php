<?php
namespace App\Form;

use App\Entity\Etablissement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Controller\EtablissementController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EtablissementType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom', TextType::class, array(
            'label' => 'Nom'
        ))
            ->add('type', TextType::class, array(
            'label' => 'Type'
        ))
            ->add('code_logistique', TextType::class, array(
            'label' => 'Code logistique'
        ))
            ->add('nb_lit', IntegerType::class, array(
            'label' => 'Nombre de lits'
        ))
            ->add('statut_convention', ChoiceType::class, array(
            'label' => 'Statut de la convention',
            'choices' => array_flip($options['statut_convention'])
        ))
            ->add('date_collaboration', DateType::class, array(
            'label' => 'Date de début de collaboration',
            'widget' => 'choice',
            'format' => 'dd MM yyyy',
            'years' => range(date('Y') - 20, date('Y'))
        ))
            ->add('numero_voie', IntegerType::class, array(
            'label' => 'Numéro de voie'
        ))
            ->add('voie', TextType::class, array(
            'label' => 'Voie'
        ))
            ->add('ville', TextType::class, array(
            'label' => 'Ville'
        ))
            ->add('code_postal', IntegerType::class, array(
            'label' => 'Code postal'
        ))
            ->add('region', TextType::class, array(
            'label' => 'Région'
        ));

        if ($options['avec_specialites']) {
            $builder->add('specialites', CollectionType::class, array(
                'label' => 'Spécialités',
                'entry_type' => SpecialiteType::class,
                'entry_options' => array(
                    'label' => ' ',
                    'avec_bouton' => false,
                    'avec_etablissement' => false
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
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Etablissement::class,
            'label_submit' => 'Valider',
            'avec_specialites' => true,
            'statut_convention' => EtablissementController::STATUT_CONVENTION
        ]);
    }
}
