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
                'setCorps' => 'Bonjour Emilie, j\'espère que vous allez bien, avez-vous des nouvelles de l\'agence ONEPOINT ? Cdt, Eric',
                'setTitre' => 'Nouvelles',
                'setDateEnvoi' => '2018-02-14 16:04:09',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-2' => [
                'setExpediteur' => 'Membre-2',
                'setDestinataire' => 'Membre-1',
                'setGroupe' => 'Groupe-1',
                'setCorps' => 'Bonjour Eric, ça va très bien je vous remercie, et vous-même ? Je les aurai au téléphone cet après-midi je vous tiens informé ASAP.',
                'setTitre' => 'Re:Nouvelles',
                'setDateEnvoi' => '2018-02-14 17:30:10',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-3' => [
                'setExpediteur' => 'Membre-1',
                'setDestinataire' => 'Membre-2',
                'setGroupe' => 'Groupe-2',
                'setCorps' => 'Bonjour, as-tu validé l\'inscription de Mme Dupuis ? Cordialement, Eric Fonzy',
                'setTitre' => 'Inscription à l\'événement',
                'setDateEnvoi' => '2018-07-05 12:12:00',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-4' => [
                'setExpediteur' => 'Membre-2',
                'setDestinataire' => 'Membre-1',
                'setGroupe' => 'Groupe-2',
                'setCorps' => 'Bonjour Eric, Non, pas encore je le fais de suite. Cordialement, Emilie.',
                'setTitre' => 'Re:Inscription à l\'événement',
                'setDateEnvoi' => '2018-07-05 17:12:00',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-5' => [
                'setExpediteur' => 'Membre-17',
                'setDestinataire' => 'Membre-1',
                'setGroupe' => 'Groupe-3',
                'setCorps' => 'Bonjour Eric, peux-tu stp me préciser combien de personnes de l\'association viendront à notre rencontre mardi ? Cdt, Olga',
                'setTitre' => 'Venue représentants association',
                'setDateEnvoi' => '2018-01-11 06:03:00',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-6' => [
                'setExpediteur' => 'Membre-1',
                'setDestinataire' => 'Membre-17',
                'setGroupe' => 'Groupe-3',
                'setCorps' => 'Bonjour Olga, oui bien sûr. Ils seront 17 au total, prévoyez un petit buffet je vous prie. Cdt, Eric',
                'setTitre' => 'Br: Re: Venue représentants association',
                'setDateEnvoi' => '2018-01-11 08:42:00',
                'setDisabled' => 0,
                'setBrouillon' => 1
            ],
            'Message-7' => [
                'setExpediteur' => 'Membre-17',
                'setDestinataire' => 'Membre-1',
                'setGroupe' => 'Groupe-3',
                'setCorps' => 'Eric, pouvez-vous me communiquer le contact d\'Emilie Ratier je vous prie ?',
                'setTitre' => 'Contact',
                'setDateEnvoi' => '2018-04-02 10:20:07',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-8' => [
                'setExpediteur' => 'Membre-1',
                'setDestinataire' => 'Membre-17',
                'setGroupe' => 'Groupe-3',
                'setCorps' => 'Bonjour Olga, merci de me renvoyer complété le listing des participants à l\'apéritif déjeunatoire du 24/09',
                'setTitre' => 'Liste participants déjeuner 24/09',
                'setDateEnvoi' => '2018-02-12 07:02:00',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-9' => [
                'setExpediteur' => 'Membre-28',
                'setDestinataire' => 'Membre-27',
                'setGroupe' => 'Groupe-4',
                'setCorps' => 'Bonjour Marie, es-tu dispo au déjeuner ? j\'aurais souhaité te faire voir ma présentation. Respectueusement, Edouard',
                'setTitre' => 'Ma présentation PPT',
                'setDateEnvoi' => '2018-07-06 13:01:36',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-10' => [
                'setExpediteur' => 'Membre-27',
                'setDestinataire' => 'Membre-28',
                'setGroupe' => 'Groupe-4',
                'setCorps' => 'Salut Edouard, Ok, plutôt vers 13h si ça te va. Marie',
                'setTitre' => 'Br: Re: Ma présentation PPT',
                'setDateEnvoi' => '2018-07-06 14:08:12',
                'setDisabled' => 0,
                'setBrouillon' => 1
            ],
            'Message-11' => [
                'setExpediteur' => 'Membre-28',
                'setDestinataire' => 'Membre-27',
                'setGroupe' => 'Groupe-4',
                'setCorps' => 'Marie, seras-tu présente pour la soirée d\'Halloween ?',
                'setTitre' => 'Soirée Halloween',
                'setDateEnvoi' => '2017-10-01 06:30:08',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-12' => [
                'setExpediteur' => 'Membre-27',
                'setDestinataire' => 'Membre-28',
                'setGroupe' => 'Groupe-4',
                'setCorps' => 'Edouard, peux-tu me renvoyer les fichiers du RDV de la semaine passée avec GIFR ? Par avance, merci',
                'setTitre' => 'Docs RDV GIFR',
                'setDateEnvoi' => '2018-06-01 09:30:08',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-13' => [
                'setExpediteur' => 'Membre-7',
                'setDestinataire' => 'Membre-1',
                'setGroupe' => 'Groupe-5',
                'setCorps' => 'Salut Eric, tu trouveras en PJ les photos de l\'évènement "Après-midi Crêpes", à transmettre à l\'agence de pub. A+ à la machine à café. Julie',
                'setTitre' => 'Photos évènement crêpes',
                'setDateEnvoi' => '2018-08-29 13:54:08',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-14' => [
                'setExpediteur' => 'Membre-1',
                'setDestinataire' => 'Membre-7',
                'setGroupe' => 'Groupe-5',
                'setCorps' => 'Salut Julie, trop sympa les photos. See you, Eric',
                'setTitre' => 'Re: Photos évènement crêpes',
                'setDateEnvoi' => '2018-08-29 15:00:02',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-15' => [
                'setExpediteur' => 'Membre-14',
                'setDestinataire' => 'Membre-8',
                'setGroupe' => 'Groupe-6',
                'setCorps' => 'Bonjour Michel, pouvez-vous je vous prie me renvoyer votre RIB ? Cdt, Kamila',
                'setTitre' => 'Demande de RIB (service comptabilité)',
                'setDateEnvoi' => '2018-07-19 14:06:02',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-16' => [
                'setExpediteur' => 'Membre-10',
                'setDestinataire' => 'Membre-1',
                'setGroupe' => 'Groupe-0',
                'setCorps' => 'Bonjour Eric, ça te dit que l\'on déjeune ensemble ce midi pour débriefer de l\'animation Food Trucks ? Cdt, Gaëtan',
                'setTitre' => 'Suivi animation Food Trucks',
                'setDateEnvoi' => '2018-08-29 14:06:02',
                'setDisabled' => 0,
                'setBrouillon' => 0
            ],
            'Message-17' => [
                'setExpediteur' => 'Membre-14',
                'setDestinataire' => 'Membre-8',
                'setGroupe' => 'Groupe-6',
                'setCorps' => 'Bonjour Michel, pouvez-vous je vous prie me renvoyer votre RIB ? Cdt, Kamila',
                'setTitre' => 'Rappel : Demande de RIB (service comptabilité)',
                'setDateEnvoi' => '2018-07-20 15:36:07',
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