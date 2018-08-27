<?php
namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\GroupeMembre;
use App\Entity\Groupe;
use App\Entity\Membre;

// use Symfony\Component\DependencyInjection\ContainerInterface;
class LoadGroupeMembreData extends Fixture implements DependentFixtureInterface
{

    private $tabGroupeMembre = [
        'GroupeMembre' => [
            'GroupeMembre-1' => [
                'setMembre' => 'Membre-1',
                'setGroupe' => 'Groupe-1',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-2' => [
                'setMembre' => 'Membre-2',
                'setGroupe' => 'Groupe-1',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-3' => [
                'setMembre' => 'Membre-1',
                'setGroupe' => 'Groupe-2',
                'setDate' => '2018-03-05 12:12:00'
            ],
            'GroupeMembre-4' => [
                'setMembre' => 'Membre-2',
                'setGroupe' => 'Groupe-2',
                'setDate' => '2018-03-05 12:12:00'
            ],
            'GroupeMembre-5' => [
                'setMembre' => 'Membre-17',
                'setGroupe' => 'Groupe-3',
                'setDate' => '2018-11-04 10:03:13'
            ],
            'GroupeMembre-6' => [
                'setMembre' => 'Membre-1',
                'setGroupe' => 'Groupe-3',
                'setDate' => '2018-11-04 10:03:13'
            ],
            'GroupeMembre-7' => [
                'setMembre' => 'Membre-27',
                'setGroupe' => 'Groupe-4',
                'setDate' => '2018-12-01 09:30:08'
            ],
            'GroupeMembre-8' => [
                'setMembre' => 'Membre-28',
                'setGroupe' => 'Groupe-4',
                'setDate' => '2018-12-01 09:30:08'
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
        $groupeMembreArray = $this->tabGroupeMembre['GroupeMembre'];
        foreach ($groupeMembreArray as $name => $value) {
            $groupeMembre = new GroupeMembre();
            $groupe = new Groupe();
            $membre = new Membre();

            foreach ($value as $key => $val) {
                if ($key == 'setDate') {
                    $val = new \DateTime($val);
                }

                if ($key == 'setMembre') {
                    $membre = $this->getReference($val);
                    $groupeMembre->{$key}($membre);
                } elseif ($key == 'setGroupe') {
                    $groupe = $this->getReference($val);
                    $groupeMembre->{$key}($groupe);
                } else {
                    $groupeMembre->{$key}($val);
                }
            }
            $manager->persist($groupeMembre);
            $this->addReference($name, $groupeMembre);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadGroupeData::class,
            LoadMembreData::class
        );
    }
}