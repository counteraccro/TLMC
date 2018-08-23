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