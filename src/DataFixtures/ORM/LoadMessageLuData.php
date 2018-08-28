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
                'setDate' => '2018-02-23 17:53:00'
            ],
            'MessageMembre-2' => [
                'setMembre' => 'Membre-2',
                'setMessage' => 'Message-1',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'MessageMembre-3' => [
                'setMembre' => 'Membre-1',
                'setMessage' => 'Message-2',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'MessageMembre-4' => [
                'setMembre' => 'Membre-2',
                'setMessage' => 'Message-2',
                'setCorbeille' => '0',
                'setLu' => '1',
                'setDate' => '2018-02-23 17:53:00'
            ],
            'MessageMembre-5' => [
                'setMembre' => 'Membre-1',
                'setMessage' => 'Message-7',
                'setCorbeille' => '1',
                'setLu' => '0',
                'setDate' => '2018-03-05 14:17:00'
            ],
            'MessageMembre-6' => [
                'setMembre' => 'Membre-27',
                'setMessage' => 'Message-11',
                'setCorbeille' => '1',
                'setLu' => '0',
                'setDate' => '2018-12-01 09:30:08'
            ],
            'MessageMembre-7' => [
                'setMembre' => 'Membre-2',
                'setMessage' => 'Message-3',
                'setCorbeille' => '1',
                'setLu' => '1',
                'setDate' => '2018-08-23 17:12:50'
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