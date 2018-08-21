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
                'setCorps' => "On s'est éclatés :)",
                'setPrenomTemoin' => 'Suzy',
                'setLienParente' => 2,
                'setVille' => 'Paris',
                'setAge' => 51,
                'setDisabled' => 1,
                'setDateCreation' => '2018-01-05 13:18:32'
            ],
            'Temoignage-2' => [
                'setEvenement' => 'Evenement-1',
                'setMembre' => 'Membre-2',
                'setTitre' => 'Un plaisir',
                'setCorps' => 'Le repas était très bon.',
                'setPrenomTemoin' => 'Henriette',
                'setLienParente' => 2,
                'setVille' => 'Paris',
                'setAge' => 62,
                'setDisabled' => 0,
                'setDateCreation' => '2018-01-05 13:18:32'
            ],
            'Temoignage-3' => [
                'setProduit' => 'Produit-1',
                'setMembre' => 'Membre-2',
                'setTitre' => 'Super cadeau',
                'setCorps' => "J'ai adoré ce jeu, merci beaucoup!!! :)",
                'setPrenomTemoin' => 'Brice',
                'setLienParente' => 3,
                'setVille' => 'Nice',
                'setAge' => 8,
                'setDisabled' => 1,
                'setDateCreation' => '2018-05-23 17:45:12'
            ],
            'Temoignage-4' => [
                'setProduit' => 'Produit-2',
                'setMembre' => 'Membre-2',
                'setTitre' => 'Super cadeau',
                'setCorps' => "J'y joue dès que je peux avec mes amis, c'est super drôle.",
                'setPrenomTemoin' => 'Kevin',
                'setLienParente' => 3,
                'setVille' => 'Toulon',
                'setAge' => 25,
                'setDisabled' => 0,
                'setDateCreation' => '2018-05-23 09:12:13'
            ],
            'Temoignage-5' => [
                'setEvenement' => 'Evenement-1',
                'setMembre' => 'Membre-3',
                'setTitre' => 'Un magnifique moment entre frères et soeurs',
                'setCorps' => "Un dernier petit mot sur ce qui nous est apparu comme essentiel : associer les frères et sœurs ; ils ont souffert ensemble, de manière différente certes, mais aujourd’hui vous leur permettez de rêver ensemble.",
                'setPrenomTemoin' => 'Dominique',
                'setLienParente' => 2,
                'setVille' => 'Strasbourg',
                'setAge' => 45,
                'setDisabled' => 0,
                'setDateCreation' => '2018-06-23 21:12:13'
            ],
            'Temoignage-6' => [
                'setEvenement' => 'Evenement-5',
                'setMembre' => 'Membre-18',
                'setTitre' => 'Mon frère, ma fierté',
                'setCorps' => "Mon frère est ma plus grande fierté… Je n’en ai parlé à personne depuis ce mail… C’est grâce à vous qu’aujourd’hui ça sort. Je m’en suis rendu compte lors, notamment, du discours de Nicolas au concert du samedi. Je me suis rendue compte que maintenant cette période est finie, que le mal est passé et surtout que je ne suis pas SEULE ! Même si je ne suis pas malade, vous m’avez soigné de cette maladie à travers mon frère ! C’est pour ça que, quand on vous dit ‘‘merci’’, ce n’est pas un mot envoyé en l’air, c’est un cri du cœur ! Vous vous rendez compte de ce que vous avez fait pour nous ?? C’est MAGNIFIQUE ",
                'setPrenomTemoin' => 'Manon',
                'setLienParente' => 4,
                'setVille' => 'Marseille',
                'setAge' => 14,
                'setDisabled' => 0,
                'setDateCreation' => '2018-06-23 21:12:13'
            ],
            'Temoignage-7' => [
                'setEvenement' => 'Evenement-5',
                'setMembre' => 'Membre-25',
                'setTitre' => 'Artistes formidables',
                'setCorps' => "Les artistes ont fait preuve d’une grande disponibilité et gentillesse vis à vis des enfants. Quelle joie de les voir revenir dans la salle de l’Olympia en s’exclamant \"Tu ne devineras jamais qui j’ai vu ?…ou avec qui j’ai pris une photo ?\" Les enfants ne touchaient plus terre, et c’est bien là la réelle définition d’un Rêve…",
                'setPrenomTemoin' => 'Silas',
                'setLienParente' => 1,
                'setVille' => 'Limoges',
                'setAge' => 56,
                'setDisabled' => 0,
                'setDateCreation' => '2018-06-23 21:12:13'
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