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
                'setDateEnvoi' => '2018-02-14 16:00:00',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-2' => [
                'setExpediteur' => 'Membre-2',
                'setDestinataire' => 'Membre-1',
                'setGroupe' => 'Groupe-1',
                'setCorps' => 'Coucou Eric, ça va très bien et toi ?',
                'setTitre' => 'Re:Nouvelles',
                'setDateEnvoi' => '2018-08-28 07:00:00',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-3' => [
                'setExpediteur' => 'Membre-1',
                'setDestinataire' => 'Membre-2',
                'setGroupe' => 'Groupe-2',
                'setCorps' => 'Bonjour, as-tu validé l\'inscription de Mme Oréo ? Cordialement, Eric Fonzy',
                'setTitre' => 'Inscription à l\'événement',
                'setDateEnvoi' => '2018-08-23 12:12:00',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-4' => [
                'setExpediteur' => 'Membre-2',
                'setDestinataire' => 'Membre-1',
                'setGroupe' => 'Groupe-2',
                'setCorps' => 'Bonjour Eric, Non, pas encore je le fais de suite. Cordialement, Emilie. At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat. At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat. At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat. At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat. At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat."',
                'setTitre' => 'Re:Inscription à l\'événement',
                'setDateEnvoi' => '2018-07-05 17:12:00',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-5' => [
                'setExpediteur' => 'Membre-17',
                'setDestinataire' => 'Membre-1',
                'setGroupe' => 'Groupe-3',
                'setCorps' => 'Message d\'Olga pour Eric',
                'setTitre' => 'Test MESSAGE admin',
                'setDateEnvoi' => '2018-08-27 16:03:00',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-6' => [
                'setExpediteur' => 'Membre-1',
                'setDestinataire' => 'Membre-17',
                'setGroupe' => 'Groupe-3',
                'setCorps' => '*BROUILLON* Re: Message brouillon d\'Eric pour Olga',
                'setTitre' => 'Test BROUILLON admin',
                'setDateEnvoi' => '2018-01-11 08:42:00',
                'setDisabled' => 0,
                'setBrouillon' => 1
            ],
            'Message-7' => [
                'setExpediteur' => 'Membre-17',
                'setDestinataire' => 'Membre-1',
                'setGroupe' => 'Groupe-3',
                'setCorps' => '*CORBEILLE* Message corbeille d\'Olga pour Eric',
                'setTitre' => 'Test CORBEILLE admin',
                'setDateEnvoi' => '2018-04-02 10:20:07',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-8' => [
                'setExpediteur' => 'Membre-1',
                'setDestinataire' => 'Membre-17',
                'setGroupe' => 'Groupe-3',
                'setCorps' => '*ENVOYE* Message envoye d\'Eric pour Olga',
                'setTitre' => 'Test ENVOYE admin',
                'setDateEnvoi' => '2018-02-12 07:02:00',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-9' => [
                'setExpediteur' => 'Membre-28',
                'setDestinataire' => 'Membre-27',
                'setGroupe' => 'Groupe-4',
                'setCorps' => 'Message d\'Edouard pour Marie',
                'setTitre' => 'Test MESSAGE user',
                'setDateEnvoi' => '2018-01-12 18:01:36',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-10' => [
                'setExpediteur' => 'Membre-27',
                'setDestinataire' => 'Membre-28',
                'setGroupe' => 'Groupe-4',
                'setCorps' => '*BROUILLON* Re: Message brouillon de Marie pour Edouard',
                'setTitre' => 'Test BROUILLON user',
                'setDateEnvoi' => '2018-07-06 14:08:12',
                'setDisabled' => 0,
                'setBrouillon' => 1
            ],
            'Message-11' => [
                'setExpediteur' => 'Membre-28',
                'setDestinataire' => 'Membre-27',
                'setGroupe' => 'Groupe-4',
                'setCorps' => '*CORBEILLE* Message corbeille d\'Edouard pour Marie',
                'setTitre' => 'Test CORBEILLE user',
                'setDateEnvoi' => '2018-10-01 06:30:08',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-12' => [
                'setExpediteur' => 'Membre-27',
                'setDestinataire' => 'Membre-28',
                'setGroupe' => 'Groupe-4',
                'setCorps' => '*ENVOYE* Message envoye par Marie pour Edouard',
                'setTitre' => 'Test ENVOYE user',
                'setDateEnvoi' => '2018-12-01 09:30:08',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
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