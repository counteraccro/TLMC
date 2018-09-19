<?php
namespace App\Form;

use App\Entity\ProduitEtablissement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Etablissement;
use App\Entity\Produit;
use App\Repository\EtablissementRepository;
use App\Repository\ProduitRepository;

class ProduitEtablissementType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['avec_etablissement']) {
            $opt_etablissement = array(
                'class' => Etablissement::class,
                'choice_label' => 'nom',
                'query_builder' => function (EtablissementRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->leftJoin('App:Specialite', 's', 'WITH', 's.etablissement = e.id')
                        ->andWhere('s.id IS NULL')
                        ->andWhere('e.disabled = 0')
                        ->orderBy('e.nom', 'ASC');
                },
                'disabled' => $options['disabled_etablissement']
            );

            if (! is_null($options['query_etablissement'])) {
                $opt_etablissement['query_builder'] = $options['query_etablissement'];
            }

            $builder->add('etablissement', EntityType::class, $opt_etablissement);
        }

        if ($options['avec_produit']) {
            $opt_produit = array(
                'class' => Produit::class,
                'choice_label' => 'titre',
                'disabled' => $options['disabled_produit'],
                'query_builder' => function (ProduitRepository $pr) {
                    return $pr->createQueryBuilder('p')
                        ->andWhere('p.disabled = 0')
                        ->orderBy('p.titre', 'ASC');
                }
            );

            if (! is_null($options['query_produit'])) {
                $opt_produit['query_builder'] = $options['query_produit'];
            }

            $builder->add('produit', EntityType::class, $opt_produit);
        }
        
        $builder->add('quantite', IntegerType::class, array(
            'label' => 'Quantité'
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
        $resolver->setDefaults([
            'data_class' => ProduitEtablissement::class,
            'label_submit' => 'Valider',
            'avec_bouton' => true,
            'avec_etablissement' => true,
            'avec_produit' => true,
            'disabled_etablissement' => false,
            'disabled_produit' => false,
            'query_etablissement' => null,
            'query_produit' => null
        ]);
    }
}
