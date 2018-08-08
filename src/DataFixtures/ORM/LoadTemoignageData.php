<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Temoignage;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Evenement;
use App\Entity\Membre;
use App\Entity\Produit;

class LoadTemoignageData extends Fixture implements DependentFixtureInterface
{

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    private $tabTemoignages = [
        'Temoignage' => [
            'Temoignage-1' => [
                'setEvenement' => 'Evenement-1',
                'setMembre' => 'Membre-1',
                'setTitre' => 'Super déjeuner',
                'setCorps' => 'On s\'est éclatés :)',
                'setPrenomTemoin' => 'Suzy',
                'setLienParente' => '2',
                'setVille' => 'Paris',
                'setAge' => '51',
                'setDisabled' => 1,
                'setDateCreation' => '2018-01-05 13:18:32'
            ],
            'Temoignage-2' => [
                'setEvenement' => 'Evenement-1',
                'setMembre' => 'Membre-2',
                'setTitre' => 'Un plaisir',
                'setCorps' => 'Le repas était très bon.',
                'setPrenomTemoin' => 'Henriette',
                'setLienParente' => '2',
                'setVille' => 'Paris',
                'setAge' => '62',
                'setDisabled' => 0,
                'setDateCreation' => '2018-01-05 13:18:32'
            ],
            'Temoignage-3' => [
                'setProduit' => 'Produit-1',
                'setMembre' => 'Membre-2',
                'setTitre' => 'Super cadeau',
                'setCorps' => 'J\'ai adoré ce jeu, merci beaucoup!!! :)',
                'setPrenomTemoin' => 'Brice',
                'setLienParente' => '3',
                'setVille' => 'Nice',
                'setAge' => '8',
                'setDisabled' => 1,
                'setDateCreation' => '2018-05-23 17:45:12'
            ],
            'Temoignage-4' => [
                'setProduit' => 'Produit-2',
                'setMembre' => 'Membre-2',
                'setTitre' => 'Super cadeau',
                'setCorps' => 'J\'y joue dès que je peux avec mes amis, c\'est super drôle.',
                'setPrenomTemoin' => 'Kevin',
                'setLienParente' => '3',
                'setVille' => 'Toulon',
                'setAge' => '25',
                'setDisabled' => 0,
                'setDateCreation' => '2018-05-23 09:12:13'
            ]
        ]
    ];

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

		//$temoignagesArray = $this->container->getParameter('Temoignage');
		$temoignagesArray = $this->tabTemoignages['Temoignage'];
		
		foreach ($temoignagesArray as $name => $object) {
            $temoignage = new Temoignage();
            $evenement = new Evenement();
            $produit = new Produit();
            $membre = new Membre();

            foreach ($object as $key => $val) {
                
                if($key == 'setDateCreation')
                {
                    $val = new \DateTime($val);
                }
                
                if ($key == 'setEvenement') {
                    $evenement = $this->getReference($val);
                    $temoignage->{$key}($evenement);
                } elseif ($key == 'setProduit') {
                    $produit = $this->getReference($val);
                    $temoignage->{$key}($produit);
                } elseif ($key == 'setMembre') {
                    $membre = $this->getReference($val);
                    $temoignage->{$key}($membre);
                } else {
                    $temoignage->{$key}($val);
                }
            }

            $manager->persist($temoignage);
            $this->addReference($name, $temoignage);
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadEvenementData::class,
            LoadMembreData::class,
            LoadProduitData::class
        );
    }
}