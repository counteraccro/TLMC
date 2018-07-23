<?php
namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\PieceJointe;
use App\Entity\Message;

// use Symfony\Component\DependencyInjection\ContainerInterface;
class LoadPieceJointeData extends Fixture implements DependentFixtureInterface
{

    private $tabPieceJointe = [
        'PieceJointe' => [
            'PieceJointe-1' => [
                'setMessage' => 'Message-1',
                'setTitre' => 'Sapin',
                'setFile' => 'Mon beau sapin',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'PieceJointe-2' => [
                'setMessage' => 'Message-2',
                'setTitre' => 'Guirlande',
                'setFile' => 'Boules de neige',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'PieceJointe-3' => [
                'setMessage' => 'Message-3',
                'setTitre' => 'Inscription Oreo',
                'setFile' => 'info_oreo.jpg',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'PieceJointe-4' => [
                'setMessage' => 'Message-3',
                'setTitre' => 'Info Oreo',
                'setFile' => 'oreo_info.pdf',
                'setDate' => '2018-02-23 17:53:00'
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
        $pieceJointeArray = $this->tabPieceJointe['PieceJointe'];
        foreach ($pieceJointeArray as $name => $value) {
            $pieceJointe = new PieceJointe();
            $message = new Message();

            foreach ($value as $key => $val) {
                if ($key == 'setDate') {
                    $val = new \DateTime($val);
                }

                if ($key == 'setMessage') {
                    $message = $this->getReference($val);
                    $pieceJointe->{$key}($message);
                } else {
                    $pieceJointe->{$key}($val);
                }
            }
            $manager->persist($pieceJointe);
            $this->addReference($name, $pieceJointe);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadMessageData::class
        );
    }
}