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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Famille;
use App\Repository\FamilleRepository;

class TemoignageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titre')->add('corps', TextareaType::class);

        if ($options['avec_event']) {
            $event_opt = array(
                'class' => Evenement::class,
                'label' => 'Evénement',
                'disabled' => $options['disabled_event'],
                'choice_label' => 'nom',
                'required' => $options['required_event']
            );

            if (! is_null($options['query_event'])) {
                $event_opt['query_builder'] = $options['query_event'];
            }

            if (! $options['add']) {
                $builder->add('famille', EntityType::class, array(
                    'class' => Famille::class,
                    'label' => 'Témoin',
                    'choice_label' => function (Famille $famille) {
                        return $famille->getPrenom() . ' ' . $famille->getNom();
                    },
                    'disabled' => $options['disabled_event'],
                    'required' => $options['required_event'],
                    'query_builder' => function (FamilleRepository $fr) {
                        return $fr->createQueryBuilder('f')
                            ->innerJoin('App:Patient', 'p', 'WITH', 'f.patient = p.id')
                            ->orderBy('p.nom ASC, p.prenom ASC, f.nom ASC, f.prenom', 'ASC');
                    },
                    'group_by' => function (Famille $famille) {
                        return $famille->getPatient()
                            ->getPrenom() . ' ' . $famille->getPatient()
                            ->getNom();
                    }
                ));
            }

            $builder->add('evenement', EntityType::class, $event_opt);
        }

        if ($options['avec_prod']) {
            $prod_opt = array(
                'class' => Produit::class,
                'disabled' => $options['disabled_prod'],
                'choice_label' => 'titre',
                'required' => $options['required_prod']
            );

            if (! is_null($options['query_prod'])) {
                $prod_opt['query_builder'] = $options['query_prod'];
            }

            $builder->add('produit', EntityType::class, $prod_opt);
        }

        if (! $options['ajax']) {
            $builder->add('image_1', FileType::class, array(
                'label' => 'Image 1',
                'data_class' => null,
                'required' => false,
                'help' => ($options['add'] ? $options['aide_ajout'] : $options['aide_edition']),
                'attr' => array(
                    'placeholder' => 'Choisir la première image'
                )
            ));

            $builder->add('image_2', FileType::class, array(
                'label' => 'Image 2',
                'data_class' => null,
                'required' => false,
                'help' => ($options['add'] ? $options['aide_ajout'] : $options['aide_edition']),
                'attr' => array(
                    'placeholder' => 'Choisir la seconde image'
                )
            ));

            $builder->add('image_3', FileType::class, array(
                'label' => 'Image 3',
                'data_class' => null,
                'required' => false,
                'help' => ($options['add'] ? $options['aide_ajout'] : $options['aide_edition']),
                'attr' => array(
                    'placeholder' => 'Choisir la troisième image'
                )
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
            'avec_prod' => true,
            'query_prod' => null,
            'query_event' => null,
            'query_famille' => null,
            'required_prod' => false,
            'required_event' => false,
            'ajax' => false,
            'add' => true,
            'allow_extra_fields' => true,
            'aide_ajout' => 'Formats de fichier acceptés : ' . implode(', ', AppController::FORMAT_IMAGE),
            'aide_edition' => 'Ne pas remplir si vous souhaitez conserver la même image'
        ]);
    }
}
