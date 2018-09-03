<?php
namespace App\Form;

use App\Entity\Historique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Patient;
use App\Entity\Specialite;
use App\Entity\Evenement;

class HistoriqueType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['avec_patient']) {
            $builder->add('patient', EntityType::class, array(
                'class' => Patient::class,
                'choice_label' => function (Patient $patient) {
                    return $patient->getPrenom() . ' ' . $patient->getNom() . ' (' . $patient->getDateNaissance()
                        ->format('d/m/Y') . ')';
                },
                'disabled' => $options['disabled_patient']
            ));
        }

        if ($options['avec_specialite']) {
            $builder->add('specialite', EntityType::class, array(
                'label' => 'Spécialité',
                'class' => Specialite::class,
                'choice_label' => 'service',
                'group_by' => function (Specialite $specialite) {
                    return $specialite->getEtablissement()
                        ->getNom();
                },
                'disabled' => $options['disabled_specialite']
            ));
        }

        if ($options['avec_evenement']) {
            $builder->add('evenement', EntityType::class, array(
                'label' => 'Evénement',
                'class' => Evenement::class,
                'choice_label' => 'nom',
                'disabled' => $options['disabled_evenement']
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
        $resolver->setDefaults(array(
            'data_class' => Historique::class,
            'label_submit' => 'Valider',
            'avec_evenement' => true,
            'disabled_evenement' => false,
            'avec_specialite' => true,
            'disabled_specialite' => false,
            'avec_patient' => true,
            'disabled_patient' => false
        ));
    }
}
