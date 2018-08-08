<?php
namespace App\Form;

use App\Entity\Evenement;
use App\Entity\Specialite;
use App\Entity\SpecialiteEvenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Controller\SpecialiteEvenementController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SpecialiteEvenementType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $opt_spe = array(
            'label' => 'Spécialité',
            'class' => Specialite::class,
            'choice_label' => 'service',
            'disabled' => $options['disabled_specialite'],
            'group_by' => function (Specialite $specialite) {
                return $specialite->getEtablissement()->getNom();
            }
        );
        if ($options['query_specialite']) {
            $opt_spe['query_builder'] = $options['query_specialite'];
        }

        if ($options['avec_specialite']) {
            $builder->add('specialite', EntityType::class, $opt_spe);
        }
        if ($options['avec_event']) {
            $builder->add('evenement', EntityType::class, array(
                'label' => 'Evénement',
                'class' => Evenement::class,
                'choice_label' => 'nom',
                'disabled' => $options['disabled_event']
            ));
        }
        $builder->add('statut', ChoiceType::class, array(
            'choices' => array_flip($options['statut'])
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
        $resolver->setDefaults(array(
            'data_class' => SpecialiteEvenement::class,
            'label_submit' => 'Valider',
            'avec_bouton' => true,
            'avec_specialite' => true,
            'avec_event' => true,
            'disabled_specialite' => false,
            'disabled_event' => false,
            'query_specialite' => null,
            'statut' => SpecialiteEvenementController::STATUT
        ));
    }
}
