<?php
namespace App\Form;

use App\Entity\FamillePatient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Controller\AppController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Famille;
use App\Repository\FamilleRepository;
use App\Entity\Patient;
use App\Repository\PatientRepository;

class FamillePatientType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('lien_parente', ChoiceType::class, array(
            'choices' => array_flip($options['liens'])
        ));
        if ($options['avec_famille']) {
            $builder->add('famille', EntityType::class, array(
                'class' => Famille::class,
                'query_builder' => function (FamilleRepository $fr) {
                    return $fr->createQueryBuilder('famille')
                        ->orderBy('famille.nom')
                        ->addOrderBy('famille.prenom');
                },
                'choice_label' => function (Famille $famille) {
                    return $famille->getPrenom() . ' ' . $famille->getNom();
                }
            ));
        }

        if ($options['avec_patient']) {
            $builder->add('patient', EntityType::class, array(
                'class' => Patient::class,
                'query_builder' => function (PatientRepository $pr) {
                    return $pr->createQueryBuilder('patient')
                        ->orderBy('patient.nom')
                        ->addOrderBy('patient.prenom');
                },
                'choice_label' => function (Patient $patient) {
                    return $patient->getPrenom() . ' ' . $patient->getNom() . ' (' . $patient->getDateNaissance()
                        ->format('d/m/Y') . ')';
                }
            ));
        }

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
            'data_class' => FamillePatient::class,
            'allow_extra_fields' => true,
            'label_submit' => 'Valider',
            'avec_famille' => true,
            'avec_patient' => true,
            'avec_bouton' => true,
            'liens' => AppController::FAMILLE_PARENTE
        ));
    }
}
