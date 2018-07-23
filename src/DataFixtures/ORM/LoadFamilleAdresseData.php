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
                'setNumeroVoie' => '19',
                'setVoie' => 'Boulevard des Jonquilles',
                'setVille' => 'Chilly-Mazarin',
                'setCodePostal' => '91380',
                'setDisabled' => 1
            ],
            'FamilleAdresse-2' => [
                'setNumeroVoie' => '13',
                'setVoie' => 'Avenue des Coquelicots',
                'setVille' => 'Chatenay-Malabry',
                'setCodePostal' => '92290',
                'setDisabled' => 0
            ],
            'FamilleAdresse-3' => [
                'setNumeroVoie' => '178',
                'setVoie' => 'Rue des Lilas',
                'setVille' => 'Montreuil',
                'setCodePostal' => '93100',
                'setDisabled' => 1
            ],
            'FamilleAdresse-4' => [
                'setNumeroVoie' => '2',
                'setVoie' => 'Avenue Charles de Gaulle',
                'setVille' => 'Paris',
                'setCodePostal' => '75002',
                'setDisabled' => 1
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
    
