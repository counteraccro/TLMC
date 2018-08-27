<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Message;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LoadMessageData extends Fixture implements DependentFixtureInterface
{

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    private $tabMessages = [
        'Message' => [
            'Message-1' => [
                'setExpediteur' => 'Membre-1',
                'setDestinataire' => 'Membre-2',
                'setGroupe' => 'Groupe-1',
                'setCorps' => 'Coucou, ça va? Gros bisous Eric',
                'setTitre' => 'Nouvelles',
                'setDateEnvoi' => '2018-02-23 17:53:00',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-2' => [
                'setExpediteur' => 'Membre-2',
                'setDestinataire' => 'Membre-1',
                'setGroupe' => 'Groupe-1',
                'setCorps' => 'Coucou Eric, ça va très bien et toi ?',
                'setTitre' => 'Re:Nouvelles',
                'setDateEnvoi' => '2018-02-24 08:45:00',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-3' => [
                'setExpediteur' => 'Membre-1',
                'setDestinataire' => 'Membre-2',
                'setGroupe' => 'Groupe-2',
                'setCorps' => 'Bonjour, as-tu validé l\'inscription de Mme Oréo ? Cordialement, Eric Fonzy',
                'setTitre' => 'Inscription à l\'événement',
                'setDateEnvoi' => '2018-03-05 12:12:00',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-4' => [
                'setExpediteur' => 'Membre-2',
                'setDestinataire' => 'Membre-1',
                'setGroupe' => 'Groupe-2',
                'setCorps' => 'Bonjour Eric, Non, pas encore je le fais de suite. Cordialement, Emilie',
                'setTitre' => 'Re:Inscription à l\'événement',
                'setDateEnvoi' => '2018-03-05 14:12:00',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ]
        ]
    ];

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        // $messagesArray = $this->container->getParameter('Message');
        $messagesArray = $this->tabMessages['Message'];

        foreach ($messagesArray as $name => $object) {
            $message = new Message();

            foreach ($object as $key => $val) {

                switch ($key) {
                    case 'setMessage':
                    case 'setExpediteur':
                    case 'setDestinataire':
                    case 'setGroupe':
                        $val = $this->getReference($val);
                        break;

                    case 'setDateEnvoi':
                        //var_dump($val); die();
                        $val = new \DateTime($val);
                        break;
                }

                $message->{$key}($val);
            }

            $manager->persist($message);
            $this->addReference($name, $message);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadMembreData::class,
            LoadGroupeData::class
        );
    }
}