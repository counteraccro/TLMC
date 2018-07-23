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
                'setEtablissement' => 'Etablissement-1',
                'setProduit' => 'Produit-1',
                'setQuantite' => '17',
                'setDate' => '2018-07-16'
            ],
            'ProduitEtablissement-2' => [
                'setEtablissement' => 'Etablissement-2',
                'setProduit' => 'Produit-1',
                'setQuantite' => '30',
                'setDate' => '2018-07-18'
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