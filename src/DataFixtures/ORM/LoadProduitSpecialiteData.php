<?php
namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\ProduitSpecialite;
use App\Entity\Produit;
use App\Entity\Specialite;

// use Symfony\Component\DependencyInjection\ContainerInterface;
class LoadProduitSpecialiteData extends Fixture implements DependentFixtureInterface
{

    private $tabProduitSpecialite = [
        'ProduitSpecialite' => [
            'ProduitSpecialite-1' => [
                'setSpecialite' => 'Specialite-1',
                'setProduit' => 'Produit-1',
                'setQuantite' => '7',
                'setDate' => '2018-07-16'
            ],
            'ProduitSpecialite-2' => [
                'setSpecialite' => 'Specialite-2',
                'setProduit' => 'Produit-2',
                'setQuantite' => '12',
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
        $produitSpecialiteArray = $this->tabProduitSpecialite['ProduitSpecialite'];
        foreach ($produitSpecialiteArray as $name => $value) {
            $produitSpecialite = new ProduitSpecialite();
            $produit = new Produit();
            $specialite = new Specialite();

            foreach ($value as $key => $val) {
                if ($key == 'setDate') {
                    $val = new \DateTime($val);
                }

                if ($key == 'setSpecialite') {
                    $specialite = $this->getReference($val);
                    $produitSpecialite->{$key}($specialite);
                } elseif ($key == 'setProduit') {
                    $produit = $this->getReference($val);
                    $produitSpecialite->{$key}($produit);
                } else {
                    $produitSpecialite->{$key}($val);
                }
            }
            $manager->persist($produitSpecialite);
            $this->addReference($name, $produitSpecialite);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadSpecialiteData::class,
            LoadProduitData::class
        );
    }
}