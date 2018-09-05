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
use App\Repository\EvenementRepository;
use App\Repository\SpecialiteRepository;
use App\Repository\PatientRepository;

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
                'group_by' => function (Patient $patient) {
                    return $patient->getSpecialite()
                        ->getService() . ' (' . $patient->getSpecialite()
                        ->getEtablissement()->getNom() . ')';
                },
                'query_builder' => $options['query_patient'],
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
                'query_builder' => $options['query_specialite'],
                'disabled' => $options['disabled_specialite']
            ));
        }

        if ($options['avec_evenement']) {
            $builder->add('evenement', EntityType::class, array(
                'label' => 'Evénement',
                'class' => Evenement::class,
                'choice_label' => 'nom',
                'query_builder' => $options['query_evenement'],
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
            'query_evenement' => function (EvenementRepository $er) {
                return $er->createQueryBuilder('event')
                    ->andWhere('event.disabled = 0')
                    ->orderBy('event.nom', 'ASC');
            },
            'avec_specialite' => true,
            'disabled_specialite' => false,
            'query_specialite' => function (SpecialiteRepository $sr) {
                return $sr->createQueryBuilder('s')
                    ->join('s.etablissement', 'e')
                    ->andWhere('s.disabled = 0')
                    ->orderBy('e.nom', 'ASC')
                    ->addOrderBy('s.service', 'ASC');
            },
            'avec_patient' => true,
            'disabled_patient' => false,
            'query_patient' => function (PatientRepository $pr) {
                return $pr->createQueryBuilder('p')
                    ->andWhere('p.disabled = 0')
                    ->orderBy('p.nom', 'ASC')
                    ->addOrderBy('p.prenom', 'ASC');
            }
        ));
    }
}
