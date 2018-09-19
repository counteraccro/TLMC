<?php
namespace App\Form;

use App\Entity\Evenement;
use App\Entity\Specialite;
use App\Entity\SpecialiteEvenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Controller\SpecialiteEvenementController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Repository\SpecialiteRepository;
use App\Repository\EvenementRepository;

class SpecialiteEvenementType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['avec_specialite']) {
            $opt_spe = array(
                'label' => 'Spécialité',
                'class' => Specialite::class,
                'choice_label' => 'service',
                'disabled' => $options['disabled_specialite'],
                'query_builder' => function (SpecialiteRepository $sr) {
                    return $sr->createQueryBuilder('s')
                        ->innerJoin('App:Etablissement', 'e', 'WITH', 's.etablissement = e.id')
                        ->andWhere('s.disabled = 0')
                        ->orderBy('e.nom ASC, s.service', 'ASC');
                },
                'group_by' => function (Specialite $specialite) {
                    return $specialite->getEtablissement()->getNom();
                }
            );
            if ($options['query_specialite']) {
                $opt_spe['query_builder'] = $options['query_specialite'];
            }

            $builder->add('specialite', EntityType::class, $opt_spe);
        }

        if ($options['avec_evenement']) {
            $opt_evenement = array(
                'label' => 'Evénement',
                'class' => Evenement::class,
                'choice_label' => 'nom',
                'disabled' => $options['disabled_evenement'],
                'query_builder' => function (EvenementRepository $er) {
                    
                    return $er->createQueryBuilder('e')
                        ->andWhere('e.disabled = 0')
                        ->orderBy('e.nom');
                }
            );
            if ($options['query_evenement']) {
                $opt_spe['query_builder'] = $options['query_evenement'];
            }

            $builder->add('evenement', EntityType::class, $opt_evenement);
        }
        $builder->add('statut', ChoiceType::class, array(
            'choices' => array_flip($options['statut'])
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
            'avec_evenement' => true,
            'disabled_specialite' => false,
            'disabled_evenement' => false,
            'query_specialite' => null,
            'query_evenement' => null,
            'statut' => SpecialiteEvenementController::STATUT
        ));
    }
}
