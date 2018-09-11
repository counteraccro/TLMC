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
                'setType' => 1,
                'setTitre' => 'Tomb Raider',
                'setTexte' => 'Le nouveau jeu Tomb Raider',
                'setTexte2' => 'Edition limitée',
                'setTrancheAge' => array(4, 5),
                'setGenre' => 1,
                'setQuantite' => 45,
                'setDateCreation' => '2017-04-12 12:00:00',
                'setDateEnvoi' => '2017-04-12 12:00:00',
                'setDisabled' => 0,
                'setImage' => "https://s.s-bol.com/imgbase0/imagebase3/large/FC/7/4/1/8/9200000028828147.jpg"
            ],
            'Produit-2' => [
                'setType' => 1,
                'setTitre' => 'Paintball',
                'setTexte' => 'Ca va tacher !',
                'setTrancheAge' => array(5),
                'setGenre' => 0,
                'setQuantite' => 24,
                'setDateCreation' => '2018-07-17 19:00:00',
                'setDateEnvoi' => '2018-08-30 12:00:00',
                'setDisabled' => 0
            ],
            'Produit-3' => [
                'setType' => 1,
                'setTitre' => 'Crayon de couleur',
                'setTexte' => 'Set de 24 crayons de couleur',
                'setTrancheAge' => array(2, 3, 4),
                'setImage' => 'Crayon de couleur_23082018085439.jpg',
                'setGenre' => 0,
                'setQuantite' => 50,
                'setDateCreation' => '2018-07-17 19:00:00',
                'setDateEnvoi' => '2018-06-25 15:00:00',
                'setDisabled' => 1
            ],
            'Produit-4' => [
                'setType' => 2,
                'setTitre' => 'Matelas',
                'setTexte' => 'Matelas avec ressorts',
                'setTrancheAge' => array(0),
                'setGenre' => 0,
                'setQuantite' => 30,
                'setDateCreation' => '2018-07-17 19:00:00',
                'setDateEnvoi' => '2018-07-17 14:00:00',
                'setDisabled' => 0
            ],
            'Produit-5' => [
                'setType' => 2,
                'setTitre' => 'Defibrillateur',
                'setTexte' => 'Defibrillateur',
                'setTrancheAge' => array(0),
                'setGenre' => 0,
                'setQuantite' => 35,
                'setDateCreation' => '2018-07-17 19:00:00',
                'setDateEnvoi' => '2018-04-22 09:00:00',
                'setDisabled' => 1
            ],
            'Produit-6' => [
                'setType' => 2,
                'setTitre' => 'Chaise',
                'setTexte' => 'Chaise',
                'setTrancheAge' => array(0),
                'setGenre' => 0,
                'setQuantite' => 30,
                'setDateCreation' => '2016-07-17 09:28:36',
                'setDateEnvoi' => '2016-07-18 14:00:00',
                'setDisabled' => 0,
                'setImage' => 'https://www.cocktail-scandinave.fr/Vbeta2018/wp-content/uploads/2018/02/MXCHA100-zoom.jpg'
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