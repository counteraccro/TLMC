<?php
namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Etablissement;

// use Symfony\Component\DependencyInjection\ContainerInterface;
class LoadEtablissementData extends Fixture
{

    private $tabEtablissement = [
        'Etablissement' => [
            'Etablissement-1' => [
                'setNom' => 'Saint-Antoine',
                'setType' => 'Hôpital',
                'setNumeroVoie' => 127,
                'setVoie' => 'Rue des Acacias',
                'setVille' => 'Les Ulis',
                'setCodePostal' => 91830,
                'setCodeLogistique' => 'HSA_91',
                'setRegion' => 'Ile-de-France',
                'setNbLit' => 111,
                'setStatutConvention' => 1,
                'setDateCollaboration' => '1991-09-30',
                'setDisabled' => 0
            ],
            'Etablissement-2' => [
                'setNom' => 'Maison Perce-Neige',
                'setType' => 'Maison de parents',
                'setNumeroVoie' => 32,
                'setVoie' => 'Chemin des Traverses',
                'setVille' => 'Antony',
                'setCodePostal' => 92120,
                'setCodeLogistique' => 'MPN_92',
                'setRegion' => 'Ile-de-France',
                'setNbLit' => 31,
                'setStatutConvention' => 2,
                'setDateCollaboration' => '2001-03-12',
                'setDisabled' => 0
            ],
            'Etablissement-3' => [
                'setNom' => 'Hôpital Deschamps',
                'setType' => 'Hôpital',
                'setNumeroVoie' => 322,
                'setVoie' => 'Route des Coquelicots',
                'setVille' => 'Ris Orangis',
                'setCodePostal' => 91450,
                'setCodeLogistique' => 'HDC_91',
                'setRegion' => 'Ile-de-France',
                'setNbLit' => 56,
                'setStatutConvention' => 1,
                'setDateCollaboration' => '1991-09-30',
                'setDisabled' => 0
            ],
            'Etablissement-4' => [
                'setNom' => 'Maison Marie Curie',
                'setType' => 'Maison de parents',
                'setNumeroVoie' => 102,
                'setVoie' => 'rue des Cocotiers',
                'setVille' => 'Bagneux',
                'setCodePostal' => 92230,
                'setCodeLogistique' => 'MMC_92',
                'setRegion' => 'Ile-de-France',
                'setNbLit' => 25,
                'setStatutConvention' => 2,
                'setDateCollaboration' => '2005-11-25',
                'setDisabled' => 0
            ],
            'Etablissement-5' => [
                'setNom' => 'Clinique Georges 5',
                'setType' => 'Clinique',
                'setNumeroVoie' => 115,
                'setVoie' => 'Route du maréchal Petain',
                'setVille' => 'Argenteuil',
                'setCodePostal' => 95100,
                'setCodeLogistique' => 'CG5_95',
                'setRegion' => 'Ile-de-France',
                'setNbLit' => 102,
                'setStatutConvention' => 0,
                'setDateCollaboration' => '1998-02-16',
                'setDisabled' => 1
            ]
        ]
    ];

    /*
     * public function __construct(ContainerInterface $container = null)
     * {
     * $this->container = $container;
     * }
     */
    public function load(ObjectManager $manager)
    {
        $etablissementArray = $this->tabEtablissement['Etablissement'];
        foreach ($etablissementArray as $name => $value) {
            $etablissement = new Etablissement();

            foreach ($value as $key => $val) {
                if ($key == 'setDateCollaboration') {
                    $val = new \DateTime($val);
                }
                $etablissement->{$key}($val);
            }
            $manager->persist($etablissement);
            $this->addReference($name, $etablissement);
        }
        $manager->flush();
    }
}