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
            ],
            'GroupeMembre-9' => [
                'setMembre' => 'Membre-7',
                'setGroupe' => 'Groupe-5',
                'setDate' => '2018-08-29 13:50:08'
            ],
            'GroupeMembre-10' => [
                'setMembre' => 'Membre-1',
                'setGroupe' => 'Groupe-5',
                'setDate' => '2018-08-29 13:50:08'
            ],
            'GroupeMembre-11' => [
                'setMembre' => 'Membre-1',
                'setGroupe' => 'Groupe-6',
                'setDate' => '2018-08-29 14:04:17'
            ],
            'GroupeMembre-12' => [
                'setMembre' => 'Membre-14',
                'setGroupe' => 'Groupe-6',
                'setDate' => '2018-08-29 14:04:17'
            ],
            'GroupeMembre-13' => [
                'setMembre' => 'Membre-13',
                'setGroupe' => 'Groupe-6',
                'setDate' => '2018-08-31 11:39:12'
            ],
            'GroupeMembre-14' => [
                'setMembre' => 'Membre-13',
                'setGroupe' => 'Groupe-6',
                'setDate' => '2018-08-31 11:39:12'
            ],
            'GroupeMembre-15' => [
                'setMembre' => 'Membre-1',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-16' => [
                'setMembre' => 'Membre-2',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-17' => [
                'setMembre' => 'Membre-3',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-18' => [
                'setMembre' => 'Membre-4',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-19' => [
                'setMembre' => 'Membre-5',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-20' => [
                'setMembre' => 'Membre-6',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-21' => [
                'setMembre' => 'Membre-7',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-22' => [
                'setMembre' => 'Membre-8',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-23' => [
                'setMembre' => 'Membre-9',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-24' => [
                'setMembre' => 'Membre-10',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-25' => [
                'setMembre' => 'Membre-11',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-26' => [
                'setMembre' => 'Membre-12',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-27' => [
                'setMembre' => 'Membre-13',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-28' => [
                'setMembre' => 'Membre-14',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-29' => [
                'setMembre' => 'Membre-15',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-30' => [
                'setMembre' => 'Membre-16',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-31' => [
                'setMembre' => 'Membre-17',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-32' => [
                'setMembre' => 'Membre-18',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-33' => [
                'setMembre' => 'Membre-19',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-34' => [
                'setMembre' => 'Membre-20',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-35' => [
                'setMembre' => 'Membre-21',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-36' => [
                'setMembre' => 'Membre-22',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-37' => [
                'setMembre' => 'Membre-23',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-38' => [
                'setMembre' => 'Membre-24',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-39' => [
                'setMembre' => 'Membre-25',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-40' => [
                'setMembre' => 'Membre-26',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-41' => [
                'setMembre' => 'Membre-27',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-42' => [
                'setMembre' => 'Membre-28',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-43' => [
                'setMembre' => 'Membre-29',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'GroupeMembre-44' => [
                'setMembre' => 'Membre-30',
                'setGroupe' => 'Groupe-0',
                'setDate' => '2018-02-23 17:53:00'
            ],      
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
                    $groupeMembre->{$key}(new \DateTime());
                } else if ($key == 'setMembre') {
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