<?php
namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\TypeProduit;

class LoadTypeProduitData extends Fixture
{

    private $tabTypeProduit = [
        'TypeProduit' => [
            'TypeProduit-1' => [
                'setNom' => 'Cadeau',
                'setDisabled' => 0
            ],
            'TypeProduit-2' => [
                'setNom' => 'Matériel',
                'setDisabled' => 0
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
        $typeProduitArray = $this->tabTypeProduit['TypeProduit'];
        foreach ($typeProduitArray as $name => $value) {
            $type_produit = new TypeProduit();

            foreach ($value as $key => $val) {
                $type_produit->{$key}($val);
            }
            $manager->persist($type_produit);
            $this->addReference($name, $type_produit);
        }
        $manager->flush();
    }
}