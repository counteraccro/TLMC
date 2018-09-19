<?php
namespace App\Form;

use App\Entity\ProduitSpecialite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Specialite;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\SpecialiteRepository;
use App\Repository\ProduitRepository;

class ProduitSpecialiteType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        

        if ($options['avec_specialite']) {
            $opt_specialite = array(
                'label' => 'Spécialité',
                'class' => Specialite::class,
                'choice_label' => 'service',
                'disabled' => $options['disabled_specialite'],
                'query_builder' => function (SpecialiteRepository $sr) {
                return $sr->createQueryBuilder('s')
                ->innerJoin('App:Etablissement', 'e', 'WITH', 's.etablissement = e.id')
                ->andWhere('s.disabled = 0')
                ->orderBy('e.nom, s.service', 'ASC');
                },
                'group_by' => function (Specialite $specialite) {
                return $specialite->getEtablissement()->getNom();
                }
                );
            
            if (! is_null($options['query_specialite'])) {
                $opt_specialite['query_builder'] = $options['query_specialite'];
            }
            
            $builder->add('specialite', EntityType::class, $opt_specialite);
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
            'data_class' => ProduitSpecialite::class,
            'label_submit' => 'Valider',
            'avec_bouton' => true,
            'avec_specialite' => true,
            'avec_produit' => true,
            'disabled_specialite' => false,
            'disabled_produit' => false,
            'query_specialite' => null,
            'query_produit' => null
        ]);
    }
}
