<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Produit;

class LoadProduitData extends Fixture
{

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    private $tabProduits = [
        'Produit' => [
            'Produit-1' => [
                'setType' => '1',
                'setTitre' => 'Tomb Raider',
                'setTexte' => 'Le nouveau jeu Tomb Raider',
                'setTrancheAge' => '12-30',
                'setGenre' => '1',
                'setQuantite' => '45',
                'setDateCreation' => '2017-04-12 12:00:00',
                'setDateEnvoi' => '2017-04-12 12:00:00',
                'setDisabled' => 1
            ],
            'Produit-2' => [
                'setType' => '2',
                'setTitre' => 'Paintball',
                'setTexte' => 'Ca va tacher !',
                'setTrancheAge' => '25-35',
                'setGenre' => '2',
                'setQuantite' => '20',
                'setDateCreation' => '2018-07-17 19:00:00',
                'setDateEnvoi' => '2018-08-30 12:00:00',
                'setDisabled' => 0
            ]
        ]
    ];

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        // $produitsArray = $this->container->getParameter('Produit');
        $produitsArray = $this->tabProduits['Produit'];

        foreach ($produitsArray as $name => $object) {
            $produit = new Produit();

            foreach ($object as $key => $val) {

                if ($key == 'setDateEnvoi' || $key == 'setDateCreation') {
                    $val = new \DateTime($val);
                }

                $produit->{$key}($val);
            }

            $manager->persist($produit);
            $this->addReference($name, $produit);
        }
        $manager->flush();
    }
}