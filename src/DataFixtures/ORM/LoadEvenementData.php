<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Evenement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadEvenementData extends Fixture
{

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    private $tabEvenements = [
        'Evenement' => [
            'Evenement-1' => [
                'setNom' => 'Déjeuner de Noël',
                'setDateDebut' => '2017-12-25 12:00:00',
                'setDateFin' => '2017-12-25 17:00:00',
                'setNombreMax' => 32,
                'setType' => 1,
                'setImage' => 'Dejeuner de Noel_28082018100937.jpg',
                'setNomLieu' => 'Salle des fêtes',
                'setNumeroVoie' => 1,
                'setVoie' => 'rue des Sapins',
                'setVille' => 'Alaska City',
                'setCodePostal' => 99821,
                'setTrancheAge' => array(0),
                'setStatut' => 2,
                'setDateFinInscription' => '2017-12-23 17:00:00',
                'setDisabled' => 0
            ],
            'Evenement-2' => [
                'setNom' => 'Bowling Junior',
                'setDateDebut' => '2018-10-30 18:00:00',
                'setDateFin' => '2018-10-30 21:00:00',
                'setDescription' => 'Bowling (2 parties) + 2 crêpes offertes + 1 soda',
                'setInformationComplementaire' => '1 accompagnant obligatoire pour les enfants de -10 ans',
                'setNombreMax' => 12,
                'setType' => 2,
                'setImage' => 'https://images-eu.ssl-images-amazon.com/images/I/81xfN64QjVL.png',
                'setNomLieu' => 'Bowling Le Strike',
                'setNumeroVoie' => 58,
                'setVoie' => 'Boulevard de la batte',
                'setVille' => 'Montreal',
                'setCodePostal' => 568465746,
                'setTrancheAge' => array(3, 4),
                'setStatut' => 1,
                'setDateFinInscription' => '2018-09-20 00:00:00',
                'setDisabled' => 0
            ],
            'Evenement-3' => [
                'setNom' => 'Balade en cheval',
                'setDateDebut' => '2018-09-22 08:30:00',
                'setDateFin' => '2018-09-22 19:00:00',
                'setDescription' => 'Balade en cheval aux bords du Doubs. Fin de parcours en haut de la citadelle.',
                'setInformationComplementaire' => 'Il est possible pour certaine personne à mobilité réduite de faire la balade.',
                'setNombreMax' => 20,
                'setType' => 2,
                'setImage' => 'https://upload.wikimedia.org/wikipedia/commons/9/94/Besancon_boucle_Doubs.jpg',
                'setNomLieu' => 'Besançon',
                'setNumeroVoie' => 56,
                'setVoie' => 'Faubourg Rivotte',
                'setVille' => 'Besançon',
                'setCodePostal' => 25000,
                'setTrancheAge' => array(4, 5, 6),
                'setStatut' => 2,
                'setDateFinInscription' => '2018-09-16 00:00:00',
                'setDisabled' => 0
            ],
            'Evenement-4' => [
                'setNom' => "Soirée d'Halloween",
                'setDateDebut' => '2018-10-31 18:00:00',
                'setDateFin' => '2018-11-01 03:00:00',
                'setDescription' => "Venait frissoner à la soirée annuelle d'Halloween. Une distribution de bonbon et un concours de costumes seront organisés.",
                'setNombreMax' => 66,
                'setType' => 4,
                'setImage' => 'https://upload.wikimedia.org/wikipedia/fr/a/aa/Logo-halloween-commerce.png',
                'setNomLieu' => 'Salle des fêtes',
                'setNumeroVoie' => 100,
                'setVoie' => "Boulevard de l'horreur",
                'setVille' => 'Paris',
                'setCodePostal' => 75015,
                'setTrancheAge' => array(2, 3, 4),
                'setStatut' => 3,
                'setDateFinInscription' => '2018-10-14 12:00:00',
                'setDisabled' => 0
            ],
            'Evenement-5' => [
                'setNom' => "Concert de Gospel",
                'setDateDebut' => '2018-03-14 19:00:00',
                'setDateFin' => '2018-03-14 23:00:00',
                'setDescription' => "Concert de gospel 'Ma vie, ma foi' qui est en tournée dans toute la France",
                'setNombreMax' => 150,
                'setType' => 3,
                'setImage' => 'Concert de Gospel_23082018085314.jpg',
                'setNomLieu' => 'Stade des cerisiers',
                'setNumeroVoie' => 100,
                'setVoie' => "route de corbeille",
                'setVille' => 'Lardy',
                'setCodePostal' => 91450,
                'setTrancheAge' => array(0),
                'setStatut' => 1,
                'setDateFinInscription' => '2018-03-01 12:00:00',
                'setDisabled' => 0
            ],
            'Evenement-6' => [
                'setNom' => "Feu d'artifice du 14 juillet",
                'setDateDebut' => '2016-07-14 18:00:00',
                'setDateFin' => '2016-07-14 23:00:00',
                'setDescription' => "venez célébrez la fête nationale autour d'un barbecue suivi d'un feu d'artifice",
                'setNombreMax' => 150,
                'setType' => 5,
                'setNomLieu' => 'Stade des cerisiers',
                'setNumeroVoie' => 100,
                'setVoie' => "route de corbeille",
                'setVille' => 'Lardy',
                'setCodePostal' => 91450,
                'setTrancheAge' => array(0),
                'setStatut' => 4,
                'setDateFinInscription' => '2016-06-14 18:00:00',
                'setDisabled' => 1
            ]
        ]
    ];

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

		//$evenementsArray = $this->container->getParameter('Evenement');
		$evenementsArray = $this->tabEvenements['Evenement'];
		
		foreach ($evenementsArray as $name => $object) {
            $evenement = new Evenement();

            foreach ($object as $key => $val) {
                
                if($key == 'setDateDebut' || $key == 'setDateFin' || $key == 'setDateFinInscription')
                {
                    $val = new \DateTime($val);
                }
                
                $evenement->{$key}($val);
            }

            $manager->persist($evenement);
            $this->addReference($name, $evenement);
        }
        $manager->flush();
    }
}