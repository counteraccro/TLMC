<?php
namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\ProduitEtablissement;
use App\Entity\Produit;
use App\Entity\Etablissement;

// use Symfony\Component\DependencyInjection\ContainerInterface;
class LoadProduitEtablissementData extends Fixture implements DependentFixtureInterface
{

    private $tabProduitEtablissement = [
        'ProduitEtablissement' => [
            'ProduitEtablissement-1' => [
                'setEtablissement' => 'Etablissement-2',
                'setProduit' => 'Produit-4',
                'setQuantite' => 20,
                'setDate' => '2018-07-17'
            ],
            'ProduitEtablissement-2' => [
                'setEtablissement' => 'Etablissement-2',
                'setProduit' => 'Produit-5',
                'setQuantite' => 10,
                'setDate' => '2018-08-30'
            ],
            'ProduitEtablissement-3' => [
                'setEtablissement' => 'Etablissement-2',
                'setProduit' => 'Produit-6',
                'setQuantite' => 10,
                'setDate' => '2016-07-18'
            ]
        ]
    ];

    /*
     * public function __construct(ContainerInterface $container = null)
     * {
     * $this->container = $container;
     * }
     */
    public function load(ObjectManager $manager)
    {
        $produitEtablissementArray = $this->tabProduitEtablissement['ProduitEtablissement'];
        foreach ($produitEtablissementArray as $name => $value) {
            $produitEtablissement = new ProduitEtablissement();
            $produit = new Produit();
            $etablissement = new Etablissement();

            foreach ($value as $key => $val) {
                if ($key == 'setDate') {
                    $val = new \DateTime($val);
                }

                if ($key == 'setEtablissement') {
                    $etablissement = $this->getReference($val);
                    $produitEtablissement->{$key}($etablissement);
                } elseif ($key == 'setProduit') {
                    $produit = $this->getReference($val);
                    $produitEtablissement->{$key}($produit);
                } else {
                    $produitEtablissement->{$key}($val);
                }
            }
            $manager->persist($produitEtablissement);
            $this->addReference($name, $produitEtablissement);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadEtablissementData::class,
            LoadProduitData::class
        );
    }
}