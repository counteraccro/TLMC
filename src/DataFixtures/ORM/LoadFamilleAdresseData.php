<?php
namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\FamilleAdresse;

// use Symfony\Component\DependencyInjection\ContainerInterface;
class LoadFamilleAdresseData extends Fixture implements DependentFixtureInterface
{

    private $tabFamilleAdresse = [
        'FamilleAdresse' => [
            'FamilleAdresse-1' => [
                'setNumeroVoie' => 19,
                'setVoie' => 'Boulevard des Jonquilles',
                'setVille' => 'Chilly-Mazarin',
                'setCodePostal' => 91380,
                'setDisabled' => false
            ],
            'FamilleAdresse-2' => [
                'setNumeroVoie' => 13,
                'setVoie' => 'Avenue des Coquelicots',
                'setVille' => 'Chatenay-Malabry',
                'setCodePostal' => 92290,
                'setDisabled' => true
            ],
            'FamilleAdresse-3' => [
                'setNumeroVoie' => 178,
                'setVoie' => 'Rue des Lilas',
                'setVille' => 'Montreuil',
                'setCodePostal' => 93100,
                'setDisabled' => false
            ],
            'FamilleAdresse-4' => [
                'setNumeroVoie' => 2,
                'setVoie' => 'Avenue Charles de Gaulle',
                'setVille' => 'Paris',
                'setCodePostal' => 75002,
                'setDisabled' => false
            ],
            'FamilleAdresse-5' => [
                'setNumeroVoie' => 2,
                'setVoie' => 'Avenue des Etoiles',
                'setVille' => 'Paris',
                'setCodePostal' => 75017,
                'setDisabled' => false
            ],
            'FamilleAdresse-6' => [
                'setNumeroVoie' => 25,
                'setVoie' => 'rue Tolbiac',
                'setVille' => 'Paris',
                'setCodePostal' => 75013,
                'setDisabled' => false
            ],
            'FamilleAdresse-7' => [
                'setNumeroVoie' => 125,
                'setVoie' => 'Avenue de Choisy',
                'setVille' => 'Paris',
                'setCodePostal' => 75013,
                'setDisabled' => false
            ],
            'FamilleAdresse-8' => [
                'setNumeroVoie' => 11,
                'setVoie' => 'Avenue Centrale',
                'setVille' => 'Paris',
                'setCodePostal' => 75013,
                'setDisabled' => false
            ],
            'FamilleAdresse-9' => [
                'setNumeroVoie' => 255,
                'setVoie' => 'Avenue des Charettes',
                'setVille' => 'Paris',
                'setCodePostal' => 75013,
                'setDisabled' => false
            ],
            'FamilleAdresse-10' => [
                'setNumeroVoie' => 22,
                'setVoie' => 'Avenue Albert Camus',
                'setVille' => 'Paris',
                'setCodePostal' => 75018,
                'setDisabled' => false
            ],
            'FamilleAdresse-11' => [
                'setNumeroVoie' => 15,
                'setVoie' => 'rue des Abricots',
                'setVille' => 'Le Mans',
                'setCodePostal' => 72200,
                'setDisabled' => false
            ],
            'FamilleAdresse-12' => [
                'setNumeroVoie' => 15,
                'setVoie' => 'rue des Abricots',
                'setVille' => 'Le Mans',
                'setCodePostal' => 72200,
                'setDisabled' => false
            ],
            'FamilleAdresse-13' => [
                'setNumeroVoie' => 75,
                'setVoie' => 'Avenue Victor Hugo',
                'setVille' => 'Le Mans',
                'setCodePostal' => 72200,
                'setDisabled' => false
            ],
            'FamilleAdresse-14' => [
                'setNumeroVoie' => 75,
                'setVoie' => 'Boulevard Haussmann',
                'setVille' => 'Paris',
                'setCodePostal' => 75008,
                'setDisabled' => false
            ],
            'FamilleAdresse-15' => [
                'setNumeroVoie' => 75,
                'setVoie' => 'Boulevard Haussmann',
                'setVille' => 'Paris',
                'setCodePostal' => 75008,
                'setDisabled' => false
            ],
            'FamilleAdresse-16' => [
                'setNumeroVoie' => 16,
                'setVoie' => 'Avenue des Orangeades',
                'setVille' => 'Bordeaux',
                'setCodePostal' => 33000,
                'setDisabled' => false
            ],
            'FamilleAdresse-17' => [
                'setNumeroVoie' => 17,
                'setVoie' => 'Avenue Marie Currie',
                'setVille' => 'Tarbes',
                'setCodePostal' => 65000,
                'setDisabled' => false
            ],
            'FamilleAdresse-18' => [
                'setNumeroVoie' => 180,
                'setVoie' => 'Boulevard des généraux',
                'setVille' => 'Versailles',
                'setCodePostal' => 78000,
                'setDisabled' => false
            ],
            'FamilleAdresse-19' => [
                'setNumeroVoie' => 50,
                'setVoie' => 'Route de Garenne',
                'setVille' => 'Clamart',
                'setCodePostal' => 92140,
                'setDisabled' => false
            ],
            'FamilleAdresse-20' => [
                'setNumeroVoie' => 50,
                'setVoie' => 'Route de Garenne',
                'setVille' => 'Clamart',
                'setCodePostal' => 92140,
                'setDisabled' => false
            ],
            'FamilleAdresse-21' => [
                'setNumeroVoie' => 180,
                'setVoie' => 'Avenue de Lille',
                'setVille' => 'Versailles',
                'setCodePostal' => 78000,
                'setDisabled' => false
            ],
            'FamilleAdresse-22' => [
                'setNumeroVoie' => 180,
                'setVoie' => 'Avenue de Lille',
                'setVille' => 'Versailles',
                'setCodePostal' => 78000,
                'setDisabled' => false
            ]
        ]
    ];

    public function load(ObjectManager $manager)
    {
        $familleAdresseArray = $this->tabFamilleAdresse['FamilleAdresse'];
        foreach ($familleAdresseArray as $name => $value) {
            $familleAdresse = new FamilleAdresse();

            foreach ($value as $key => $val) {
                $familleAdresse->{$key}($val);
            }

            $manager->persist($familleAdresse);
            $this->addReference($name, $familleAdresse);
        }

        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadPatientData::class,
        );
    }
}
    
