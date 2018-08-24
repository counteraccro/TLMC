<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Temoignage;
use App\Entity\Evenement;
use App\Entity\Membre;
use App\Entity\Produit;
use App\Entity\Famille;

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
                'setFamille' => 'Famille-1',
                'setDisabled' => 1,
                'setDateCreation' => '2018-01-05 13:18:32'
            ],
            'Temoignage-2' => [
                'setEvenement' => 'Evenement-1',
                'setMembre' => 'Membre-2',
                'setTitre' => 'Un plaisir',
                'setCorps' => 'Le repas était très bon.',
                'setFamille' => 'Famille-2',
                'setDisabled' => 0,
                'setDateCreation' => '2018-01-05 13:18:32'
            ],
            'Temoignage-3' => [
                'setProduit' => 'Produit-1',
                'setMembre' => 'Membre-2',
                'setTitre' => 'Super cadeau',
                'setCorps' => "J'ai adoré ce jeu, merci beaucoup!!! :)",
                'setDisabled' => 1,
                'setDateCreation' => '2018-05-23 17:45:12'
            ],
            'Temoignage-4' => [
                'setProduit' => 'Produit-2',
                'setMembre' => 'Membre-2',
                'setTitre' => 'Super cadeau',
                'setCorps' => "J'y joue dès que je peux avec mes amis, c'est super drôle.",
                'setDisabled' => 0,
                'setDateCreation' => '2018-05-23 09:12:13'
            ],
            'Temoignage-5' => [
                'setEvenement' => 'Evenement-2',
                'setMembre' => 'Membre-3',
                'setTitre' => 'Un magnifique moment entre frères et soeurs',
                'setCorps' => "Un dernier petit mot sur ce qui nous est apparu comme essentiel : associer les frères et sœurs ; ils ont souffert ensemble, de manière différente certes, mais aujourd’hui vous leur permettez de rêver ensemble.",
                'setFamille' => 'Famille-1',
                'setDisabled' => 0,
                'setDateCreation' => '2018-06-23 21:12:13'
            ],
            'Temoignage-6' => [
                'setEvenement' => 'Evenement-5',
                'setMembre' => 'Membre-18',
                'setTitre' => 'Mon frère, ma fierté',
                'setCorps' => "Mon frère est ma plus grande fierté… Je n’en ai parlé à personne depuis ce mail… C’est grâce à vous qu’aujourd’hui ça sort. Je m’en suis rendu compte lors, notamment, du discours de Nicolas au concert du samedi. Je me suis rendue compte que maintenant cette période est finie, que le mal est passé et surtout que je ne suis pas SEULE ! Même si je ne suis pas malade, vous m’avez soigné de cette maladie à travers mon frère ! C’est pour ça que, quand on vous dit ‘‘merci’’, ce n’est pas un mot envoyé en l’air, c’est un cri du cœur ! Vous vous rendez compte de ce que vous avez fait pour nous ?? C’est MAGNIFIQUE ",
                'setFamille' => 'Famille-1',
                'setDisabled' => 0,
                'setDateCreation' => '2018-06-23 21:12:13'
            ],
            'Temoignage-7' => [
                'setProduit' => 'Produit-3',
                'setMembre' => 'Membre-7',
                'setTitre' => 'Super',
                'setCorps' => "Les crayons de couleurs sont trop beau, j'ai pu faire pleins de dessins avec.",
                'setDisabled' => 0,
                'setDateCreation' => '2018-01-24 17:17:17'
            ],
            'Temoignage-8' => [
                'setProduit' => 'Produit-2',
                'setMembre' => 'Membre-9',
                'setTitre' => 'Ca fait mal',
                'setCorps' => "On y a joué toute l'après-midi tellement on aimait!!!",
                'setDisabled' => 0,
                'setDateCreation' => '2018-02-12 19:30:32'
            ],
            'Temoignage-9' => [
                'setEvenement' => 'Evenement-1',
                'setMembre' => 'Membre-18',
                'setTitre' => 'Des petits enfants ravis',
                'setCorps' => "Ca faisait longtemps que je n'avais pas vu mon petit-fils et ma petite-fille rigoler comme cela. Le repas leur a fait beaucoup de bien.",
                'setFamille' => 'Famille-4',
                'setDisabled' => 0,
                'setDateCreation' => '2018-01-14 09:14:00'
            ],
            'Temoignage-10' => [
                'setEvenement' => 'Evenement-3',
                'setMembre' => 'Membre-19',
                'setTitre' => "Première leçon d'équitation",
                'setCorps' => "Ma fille a toujours voulu faire de l'équitation et grâce à TLMC, elle a pu réaliser son rêve et pour cela je suis très reconnaissant envers l'association.",
                'setFamille' => 'Famille-1',
                'setDisabled' => 0,
                'setDateCreation' => '2018-09-30 17:45:12'
            ],
            'Temoignage-11' => [
                'setEvenement' => 'Evenement-3',
                'setMembre' => 'Membre-21',
                'setTitre' => 'Balade très sympa',
                'setCorps' => "Au début, je n'étais pas très rassuré, c'est mon fils qui tenait à faire cette balade mais les paysages étaient très beaux et les animateurs vraiment gentils et prennaient bien le temps d'attendre tout le monde et répondaient à toutes nos questions. Je pense pouvoir affirmer que les petits comme les grands ont été charmés.",
                'setFamille' => 'Famille-1',
                'setDisabled' => 0,
                'setDateCreation' => '2018-10-01 10:00:00'
            ],
            'Temoignage-12' => [
                'setEvenement' => 'Evenement-4',
                'setMembre' => 'Membre-21',
                'setTitre' => 'La trouille',
                'setCorps' => "Les bonbons étaient super bons et la déco étaient trop bien! Et les costumes et les maquillages étaient magnifiques: je ressemblais à une vraie fée! ",
                'setFamille' => 'Famille-1',
                'setDisabled' => 0,
                'setDateCreation' => '2018-11-11 11:11:11'
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
            $famille = new Famille();
            $produit = new Produit();
            $membre = new Membre();

            foreach ($object as $key => $val) {
                
                switch($key){
                    case 'setEvenement':
                        $evenement = $this->getReference($val);
                        $temoignage->{$key}($evenement);
                        break;
                    case 'setProduit':
                        $produit = $this->getReference($val);
                        $temoignage->{$key}($produit);
                        break;
                    case 'setMembre':
                        $membre = $this->getReference($val);
                        $temoignage->{$key}($membre);
                        break;
                    case 'setFamille':
                        $famille = $this->getReference($val);
                        $temoignage->{$key}($famille);
                        break;
                    case 'setDateCreation':
                        $val = new \DateTime($val);
                        $temoignage->{$key}($val);
                        break;
                    default:
                        $temoignage->{$key}($val);
                        break;
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
            LoadFamilleData::class,
            LoadMembreData::class,
            LoadProduitData::class
        );
    }
}