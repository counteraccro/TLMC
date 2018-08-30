<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Historique;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Evenement;
use App\Entity\Specialite;
use App\Entity\Membre;
use App\Entity\Patient;

class LoadHistoriqueData extends Fixture implements DependentFixtureInterface
{

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    private $tabHistoriques = [
        'Historique' => [
            'Historique-1' => [
                'setEvenement' => 'Evenement-1',
                'setSpecialite' => 'Specialite-1',
                'setPatient' => 'Patient-1',
                'setMembre' => 'Membre-1',
                'setDate' => '2017-12-25 12:00:00'
            ],
            'Historique-2' => [
                'setEvenement' => 'Evenement-2',
                'setSpecialite' => 'Specialite-1',
                'setPatient' => 'Patient-3',
                'setMembre' => 'Membre-3',
                'setDate' => '2018-10-30 18:00:00'
            ],
            'Historique-3' => [
                'setEvenement' => 'Evenement-1',
                'setSpecialite' => 'Specialite-1',
                'setPatient' => 'Patient-2',
                'setMembre' => 'Membre-1',
                'setDate' => '2017-12-25 12:00:00'
            ],
            'Historique-4' => [
                'setEvenement' => 'Evenement-1',
                'setSpecialite' => 'Specialite-4',
                'setPatient' => 'Patient-7',
                'setMembre' => 'Membre-4',
                'setDate' => '2017-12-25 12:00:00'
            ],
            'Historique-4' => [
                'setEvenement' => 'Evenement-4',
                'setSpecialite' => 'Specialite-2',
                'setPatient' => 'Patient-4',
                'setMembre' => 'Membre-7',
                'setDate' => '2018-10-31 18:00:00'
            ],
            'Historique-5' => [
                'setEvenement' => 'Evenement-3',
                'setSpecialite' => 'Specialite-3',
                'setPatient' => 'Patient-5',
                'setMembre' => 'Membre-1',
                'setDate' => '2018-09-22 08:30:00'
            ],
            'Historique-6' => [
                'setEvenement' => 'Evenement-5',
                'setSpecialite' => 'Specialite-1',
                'setPatient' => 'Patient-1',
                'setMembre' => 'Membre-1',
                'setDate' => '2018-03-14 19:00:00'
            ],
            'Historique-7' => [
                'setEvenement' => 'Evenement-1',
                'setSpecialite' => 'Specialite-5',
                'setPatient' => 'Patient-7',
                'setMembre' => 'Membre-9',
                'setDate' => '2017-12-25 12:00:00'
            ],
            'Historique-8' => [
                'setEvenement' => 'Evenement-3',
                'setSpecialite' => 'Specialite-4',
                'setPatient' => 'Patient-6',
                'setMembre' => 'Membre-2',
                'setDate' => '2018-09-22 08:30:00'
            ],
            'Historique-9' => [
                'setEvenement' => 'Evenement-2',
                'setSpecialite' => 'Specialite-6',
                'setPatient' => 'Patient-8',
                'setMembre' => 'Membre-11',
                'setDate' => '2018-10-30 18:00:00'
            ],
            'Historique-10' => [
                'setEvenement' => 'Evenement-6',
                'setSpecialite' => 'Specialite-7',
                'setPatient' => 'Patient-14',
                'setMembre' => 'Membre-6',
                'setDate' => '2016-07-14 18:00:00'
            ],
            'Historique-11' => [
                'setEvenement' => 'Evenement-3',
                'setSpecialite' => 'Specialite-6',
                'setPatient' => 'Patient-9',
                'setMembre' => 'Membre-6',
                'setDate' => '2018-09-22 08:30:00'
            ],
            'Historique-12' => [
                'setEvenement' => 'Evenement-1',
                'setSpecialite' => 'Specialite-1',
                'setPatient' => 'Patient-10',
                'setMembre' => 'Membre-3',
                'setDate' => '2017-12-25 12:00:00'
            ],
            'Historique-13' => [
                'setEvenement' => 'Evenement-4',
                'setSpecialite' => 'Specialite-2',
                'setPatient' => 'Patient-11',
                'setMembre' => 'Membre-7',
                'setDate' => '2018-10-30 18:00:00'
            ],
            'Historique-14' => [
                'setEvenement' => 'Evenement-3',
                'setSpecialite' => 'Specialite-3',
                'setPatient' => 'Patient-12',
                'setMembre' => 'Membre-7',
                'setDate' => '2018-09-22 08:30:00'
            ],
            'Historique-15' => [
                'setEvenement' => 'Evenement-5',
                'setSpecialite' => 'Specialite-4',
                'setPatient' => 'Patient-13',
                'setMembre' => 'Membre-13',
                'setDate' => '2018-03-14 19:00:00'
            ]
        ]
    ];

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

		//$historiquesArray = $this->container->getParameter('Historique');
		$historiquesArray = $this->tabHistoriques['Historique'];
		
		foreach ($historiquesArray as $name => $object) {
            $historique = new Historique();
            $evenement = new Evenement();
            $specialite = new Specialite();
            $membre = new Membre();
            $patient = new Patient();

            foreach ($object as $key => $val) {
                
                if($key == 'setDate')
                {
                    $val = new \DateTime($val);
                }
                
                if ($key == 'setEvenement') {
                    $evenement = $this->getReference($val);
                    $historique->{$key}($evenement);
                } elseif ($key == 'setSpecialite') {
                    $specialite = $this->getReference($val);
                    $historique->{$key}($specialite);
                } elseif ($key == 'setMembre') {
                    $membre = $this->getReference($val);
                    $historique->{$key}($membre);
                } elseif ($key == 'setPatient') {
                    $patient = $this->getReference($val);
                    $historique->{$key}($patient);
                } else {
                    $historique->{$key}($val);
                }
            }

            $manager->persist($historique);
            $this->addReference($name, $historique);
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadEvenementData::class,
            LoadSpecialiteData::class,
            LoadPatientData::class,
            LoadMembreData::class
        );
    }
}