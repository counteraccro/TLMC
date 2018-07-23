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
                'setNombreMax' => '32',
                'setType' => '1',
                'setImage' => 'https://www.fmsb.be/sites/secure.fmsb.be/files/NoelRouge-540x300.png',
                'setNomLieu' => 'Salle des fêtes',
                'setNumeroVoie' => '1',
                'setVoie' => 'rue des Sapins',
                'setVille' => 'Alaska City',
                'setCodePostal' => '99821',
                'setTrancheAge' => '5-80',
                'setStatut' => '2',
                'setDateFinInscription' => '2017-12-23 17:00:00',
                'setDisabled' => 1
            ],
            'Evenement-2' => [
                'setNom' => 'Bowling Junior',
                'setDateDebut' => '2018-10-30 18:00:00',
                'setDateFin' => '2018-10-30 21:00:00',
                'setDescription' => 'Bowling (2 parties) + 2 crêpes offertes + 1 soda',
                'setInformationComplementaire' => '1 accompagnant obligatoire pour les enfants de -10 ans',
                'setNombreMax' => '12',
                'setType' => '2',
                'setImage' => 'https://images-eu.ssl-images-amazon.com/images/I/81xfN64QjVL.png',
                'setNomLieu' => 'Bowling Le Strike',
                'setNumeroVoie' => '58',
                'setVoie' => 'Boulevard de la batte',
                'setVille' => 'Montreal',
                'setCodePostal' => '568465746',
                'setTrancheAge' => '8-12',
                'setStatut' => '1',
                'setDateFinInscription' => '2018-09-20 00:00:00',
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