<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Groupe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Controller\AppController;

class LoadGroupeData extends Fixture
{

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    private $tabGroupes = [
        'Groupe' => [
            'Groupe-0' => [
                'setNom' => AppController::GROUPE_GLOBAL,
                'setDate' => '2018-02-23 17:53:00',
                'setDisabled' => '1'
            ],
            'Groupe-1' => [
                'setNom' => 'Nouvelles',
                'setDate' => '2018-02-23 17:53:00',
                'setDisabled' => '1'
            ],
            'Groupe-2' => [
                'setNom' => 'Inscription',
                'setDate' => '2018-03-05 12:12:00',
                'setDisabled' => '0'
            ],
            'Groupe-3' => [
                'setNom' => 'Tests avec Admin',
                'setDate' => '2018-11-04 10:03:13',
                'setDisabled' => '0'
            ],
            'Groupe-4' => [
                'setNom' => 'Tests avec User',
                'setDate' => '2018-12-01 09:30:08',
                'setDisabled' => '0'
            ],
            'Groupe-5' => [
                'setNom' => 'Entre potes [perso]',
                'setDate' => '2018-08-29 13:50:08',
                'setDisabled' => '0'
            ],
            'Groupe-6' => [
                'setNom' => 'Service Compta',
                'setDate' => '2018-08-29 14:04:17',
                'setDisabled' => '0'
            ],
        ]
    ];

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

		//$groupesArray = $this->container->getParameter('Groupe');
		$groupesArray = $this->tabGroupes['Groupe'];
		
		foreach ($groupesArray as $name => $object) {
            $groupe = new Groupe();

            foreach ($object as $key => $val) {
                
                if($key == 'setDate')
                {
                    $val = new \DateTime();
                }
                
                $groupe->{$key}($val);
            }

            $manager->persist($groupe);
            $this->addReference($name, $groupe);
        }
        $manager->flush();
    }
}