<?php
namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\MessageLu;
use App\Entity\Message;
use App\Entity\Membre;

// use Symfony\Component\DependencyInjection\ContainerInterface;
class LoadMessageLuData extends Fixture implements DependentFixtureInterface
{

    private $tabMessageMembre = [
        'MessageMembre' => [
            'MessageMembre-1' => [
                'setMembre' => 'Membre-1',
                'setMessage' => 'Message-1',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-02-14 16:00:00'
            ],
            'MessageMembre-2' => [
                'setMembre' => 'Membre-2',
                'setMessage' => 'Message-1',
                'setCorbeille' => '0',
                'setLu' => '0',
                'setDate' => '2018-02-14 17:00:00'
            ],
            'MessageMembre-3' => [
                'setMembre' => 'Membre-2',
                'setMessage' => 'Message-2',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-08-28 07:00:00'
            ],
            'MessageMembre-4' => [
                'setMembre' => 'Membre-1',
                'setMessage' => 'Message-2',
                'setCorbeille' => '0',
                'setLu' => '0',
                'setDate' => '2018-08-28 08:00:00'
            ],
            'MessageMembre-5' => [
                'setMembre' => 'Membre-1',
                'setMessage' => 'Message-3',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-08-23 12:12:00'
            ],
            'MessageMembre-6' => [
                'setMembre' => 'Membre-2',
                'setMessage' => 'Message-3',
                'setCorbeille' => '1',
                'setLu' => '1',
                'setDate' => '2018-08-23 13:12:00'
            ],
            'MessageMembre-7' => [
                'setMembre' => 'Membre-2',
                'setMessage' => 'Message-4',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-07-05 17:12:00'
            ],
            'MessageMembre-8' => [
                'setMembre' => 'Membre-1',
                'setMessage' => 'Message-4',
                'setCorbeille' => '0',
                'setLu' => '0',
                'setDate' => '2018-07-05 18:12:00'
            ],
            'MessageMembre-9' => [
                'setMembre' => 'Membre-17',
                'setMessage' => 'Message-5',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-08-27 16:03:00'
            ],
            'MessageMembre-10' => [
                'setMembre' => 'Membre-1',
                'setMessage' => 'Message-5',
                'setCorbeille' => '0',
                'setLu' => '0',
                'setDate' => '2018-08-27 17:03:00'
            ],
            'MessageMembre-11' => [
                'setMembre' => 'Membre-1',
                'setMessage' => 'Message-6',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-01-11 08:42:00'
            ],
            'MessageMembre-12' => [
                'setMembre' => 'Membre-17',
                'setMessage' => 'Message-6',
                'setCorbeille' => '0',
                'setLu' => '0',
                'setDate' => '2018-01-11 09:42:00'
            ],
            'MessageMembre-12' => [
                'setMembre' => 'Membre-17',
                'setMessage' => 'Message-7',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-04-02 10:20:07'
            ],
            'MessageMembre-13' => [
                'setMembre' => 'Membre-1',
                'setMessage' => 'Message-7',
                'setCorbeille' => '1',
                'setLu' => '0',
                'setDate' => '2018-04-02 11:20:07'
            ],
            'MessageMembre-14' => [
                'setMembre' => 'Membre-1',
                'setMessage' => 'Message-8',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-02-12 07:02:00'
            ],
            'MessageMembre-15' => [
                'setMembre' => 'Membre-17',
                'setMessage' => 'Message-8',
                'setCorbeille' => '0',
                'setLu' => '0',
                'setDate' => '2018-02-12 08:02:00'
            ],
            'MessageMembre-16' => [
                'setMembre' => 'Membre-28',
                'setMessage' => 'Message-9',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-01-12 18:01:36'
            ],
            'MessageMembre-17' => [
                'setMembre' => 'Membre-27',
                'setMessage' => 'Message-9',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-01-12 19:01:36'
            ],
            'MessageMembre-18' => [
                'setMembre' => 'Membre-27',
                'setMessage' => 'Message-10',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-07-06 14:08:12'
            ],
            'MessageMembre-19' => [
                'setMembre' => 'Membre-28',
                'setMessage' => 'Message-10',
                'setCorbeille' => '0',
                'setLu' => '0',
                'setDate' => '2018-07-06 15:08:12'
            ],
            'MessageMembre-20' => [
                'setMembre' => 'Membre-28',
                'setMessage' => 'Message-11',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-10-01 06:30:08'
            ],
            'MessageMembre-21' => [
                'setMembre' => 'Membre-27',
                'setMessage' => 'Message-11',
                'setCorbeille' => '1',
                'setLu' => '0',
                'setDate' => '2018-10-01 07:30:08'
            ],
            'MessageMembre-18' => [
                'setMembre' => 'Membre-27',
                'setMessage' => 'Message-12',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-12-01 09:30:08'
            ],
            'MessageMembre-19' => [
                'setMembre' => 'Membre-28',
                'setMessage' => 'Message-12',
                'setCorbeille' => '0',
                'setLu' => '0',
                'setDate' => '2018-12-01 10:30:08'
            ],
            'MessageMembre-20' => [
                'setMembre' => 'Membre-7',
                'setMessage' => 'Message-13',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-08-29 13:54:08'
            ],
            'MessageMembre-21' => [
                'setMembre' => 'Membre-1',
                'setMessage' => 'Message-13',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-08-29 14:54:08'
            ],
            'MessageMembre-22' => [
                'setMembre' => 'Membre-1',
                'setMessage' => 'Message-14',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-08-29 15:00:02'
            ],
            'MessageMembre-23' => [
                'setMembre' => 'Membre-7',
                'setMessage' => 'Message-14',
                'setCorbeille' => '0',
                'setLu' => '0',
                'setDate' => '2018-08-29 16:00:02'
            ],
            'MessageMembre-24' => [
                'setMembre' => 'Membre-14',
                'setMessage' => 'Message-15',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => ' 2018-08-29 14:06:02'
            ],
            'MessageMembre-25' => [
                'setMembre' => 'Membre-8',
                'setMessage' => 'Message-15',
                'setCorbeille' => '0',
                'setLu' => '0',
                'setDate' => ' 2018-08-29 15:06:02'
            ],          
            'MessageMembre-26' => [
                'setMembre' => 'Membre-1',
                'setMessage' => 'Message-16',
                'setCorbeille' => '0',
                'setLu' => '0',
                'setDate' => ' 2018-08-29 15:06:02'
            ],  
            'MessageMembre-27' => [
                'setMembre' => 'Membre-8',
                'setMessage' => 'Message-17',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-07-20 16:42:07',
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
        $messageLuArray = $this->tabMessageMembre['MessageMembre'];
        foreach ($messageLuArray as $name => $value) {
            $messageLu = new MessageLu();
            $message = new Message();
            $membre = new Membre();

            foreach ($value as $key => $val) {
                if ($key == 'setDate') {
                    $val = new \DateTime($val);
                }

                if ($key == 'setMembre') {
                    $membre = $this->getReference($val);
                    $messageLu->{$key}($membre);
                } elseif ($key == 'setMessage') {
                    $message = $this->getReference($val);
                    $messageLu->{$key}($message);
                } else {
                    $messageLu->{$key}($val);
                }
            }
            $manager->persist($messageLu);
            $this->addReference($name, $messageLu);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadMessageData::class,
            LoadMembreData::class
        );
    }
}