<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Membre;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoadMembreData extends Fixture implements DependentFixtureInterface
{

    /**
     *
     * @var ContainerInterface
     */
    private $container;
    
    private $encoder;

    private $tabMembres = [
        'Membre' => [
            'Membre-1' => [
                'setNom' => 'Fonzy',
                'setUsername' => 'admin',
                'setPassword' => 'admin',
                'setPrenom' => 'Eric',
                'setNumeroTel' => '+33146589275',
                'setEmail' => 'efonzydu92@gmail.com',
                'setFonction' => 'Directeur',
                'setDecideur' => true,
                'setAnnuaire' => false,
                'setSignature' => 'avec tout mon amour, votre cher Eric Fonzy',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-1',
                'setSpecialite' => 'Specialite-1',
                'setSalt' => '1A2B3C4D5E6F7G890',
                'setRoles' => array('ROLE_ADMIN')
            ],
            'Membre-2' => [
                'setNom' => 'Ratier',
                'setUsername' => 'beneficiaire',
                'setPassword' => 'beneficiaire',
                'setPrenom' => 'Emilie',
                'setNumeroTel' => '+33113226452',
                'setEmail' => 'emilieratier@gmail.com',
                'setFonction' => 'Gestionnaire',
                'setDecideur' => false,
                'setAnnuaire' => true,
                'setSignature' => 'à votre disposition, Emilie Ratier',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-3',
                'setSpecialite' => 'Specialite-4',
                'setSalt' => '1A2B3C4D5E6F7G8901',
                'setRoles' => array('ROLE_BENEFICIAIRE')
            ],
            'Membre-3' => [
                'setNom' => 'Dupuis',
                'setUsername' => 'beneficiaire_direct',
                'setPassword' => 'beneficiaire_direct',
                'setPrenom' => 'Jean-Michel',
                'setNumeroTel' => '+33146589275',
                'setEmail' => 'jean-michel.dupuis@gmail.com',
                'setFonction' => 'Directeur',
                'setDecideur' => false,
                'setAnnuaire' => true,
                'setSignature' => 'Cordialement, Directeur Jean-Michel Dupuis',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-1',
                'setSpecialite' => 'Specialite-1',
                'setSalt' => '1A2B3C4D5E6F7G890A',
                'setRoles' => array('ROLE_BENEFICIAIRE_DIRECT')
            ],
            'Membre-4' => [
                'setNom' => 'Maure',
                'setUsername' => 'benevole',
                'setPassword' => 'benevole',
                'setPrenom' => 'Jeanne',
                'setNumeroTel' => '0102030405',
                'setEmail' => 'jeanne.maure@gmail.com',
                'setFonction' => 'Bénévole',
                'setDecideur' => true,
                'setAnnuaire' => true,
                'setSignature' => 'à votre disposition, Jeanne Maure',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-3',
                'setSpecialite' => 'Specialite-4',
                'setSalt' => '1A2B3C4D5E6F7G8902',
                'setRoles' => array('ROLE_BENEVOLE')
            ],
            'Membre-5' => [
                'setNom' => 'Lucas',
                'setUsername' => 'beneficiaire2',
                'setPassword' => 'beneficiaire2',
                'setPrenom' => 'Maureen',
                'setNumeroTel' => '0106070809',
                'setEmail' => 'maureen.lucas@gmail.com',
                'setFonction' => 'Directeur adjoint',
                'setDecideur' => true,
                'setAnnuaire' => true,
                'setSignature' => 'à votre disposition, Maureen Lucas',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-2',
                'setSalt' => '1A2B3C4D5E6F7G8905',
                'setRoles' => array('ROLE_BENEFICIAIRE')
            ],
            'Membre-6' => [
                'setNom' => 'Lucas',
                'setUsername' => 'benevole2',
                'setPassword' => 'benevole2',
                'setPrenom' => 'Andréa',
                'setNumeroTel' => '0106070809',
                'setEmail' => 'andrea.lucas@gmail.com',
                'setFonction' => 'Bénévole',
                'setDecideur' => true,
                'setAnnuaire' => true,
                'setSignature' => 'à votre disposition, Andréa Lucas',
                'setDisabled' => 1,
                'setEtablissement' => 'Etablissement-5',
                'setSpecialite' => 'Specialite-6',
                'setSalt' => '1A2B3C4D5E6F7G8905',
                'setRoles' => array('ROLE_BENEVOLE')
            ],
            'Membre-7' => [
                'setNom' => 'Lombardo',
                'setUsername' => 'beneficiaire3',
                'setPassword' => 'beneficiaire3',
                'setPrenom' => 'Julie',
                'setNumeroTel' => '+33113123456',
                'setEmail' => 'jul_lombardo@gmail.com',
                'setFonction' => 'Gestionnaire',
                'setDecideur' => false,
                'setAnnuaire' => true,
                'setSignature' => 'à votre disposition, Julie Lombardo',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-1',
                'setSpecialite' => 'Specialite-3',
                'setSalt' => '1A2B3C4D5E6F7G8901',
                'setRoles' => array('ROLE_BENEFICIAIRE')
            ],
            'Membre-8' => [
                'setNom' => 'Travolta',
                'setUsername' => 'beneficiaire_direct2',
                'setPassword' => 'beneficiaire_direct2',
                'setPrenom' => 'Michel',
                'setNumeroTel' => '+33256581578',
                'setEmail' => 'michel.travolta@gmail.com',
                'setFonction' => 'Directeur',
                'setDecideur' => false,
                'setAnnuaire' => true,
                'setSignature' => 'Cordialement, Directeur Michel Travolta',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-1',
                'setSpecialite' => 'Specialite-1',
                'setSalt' => '1A2B3C4D5E6F7G890A',
                'setRoles' => array('ROLE_BENEFICIAIRE_DIRECT')
            ],
            'Membre-9' => [
                'setNom' => 'Delamare',
                'setUsername' => 'benevole3',
                'setPassword' => 'benevole3',
                'setPrenom' => 'Claudio',
                'setNumeroTel' => '0125654789',
                'setEmail' => 'delamare.claudio@gmail.com',
                'setFonction' => 'Bénévole',
                'setDecideur' => true,
                'setAnnuaire' => true,
                'setSignature' => 'à votre disposition, Claudio Delamare',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-3',
                'setSpecialite' => 'Specialite-5',
                'setSalt' => '1A2B3C4D5E6F7G8905',
                'setRoles' => array('ROLE_BENEVOLE')
            ],
            'Membre-10' => [
                'setNom' => 'Moreau',
                'setUsername' => 'beneficiaire4',
                'setPassword' => 'beneficiaire4',
                'setPrenom' => 'Gaëtan',
                'setNumeroTel' => '+33318743456',
                'setEmail' => 'gae-mor@gmail.com',
                'setFonction' => 'Gestionnaire',
                'setDecideur' => false,
                'setAnnuaire' => true,
                'setSignature' => 'à votre disposition, Gaëtan Moreau',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-2',
                'setSalt' => '1A2B3C4D5E6F7G8901',
                'setRoles' => array('ROLE_BENEFICIAIRE')
            ],
            'Membre-11' => [
                'setNom' => 'Roya',
                'setUsername' => 'beneficiaire_direct3',
                'setPassword' => 'beneficiaire_direct3',
                'setPrenom' => 'Regina',
                'setNumeroTel' => '+33456581578',
                'setEmail' => 'roya_guapita@gmail.com',
                'setFonction' => 'Directrice',
                'setDecideur' => false,
                'setAnnuaire' => true,
                'setSignature' => 'Cordialement, Directrice Regina Roya',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-3',
                'setSpecialite' => 'Specialite-5',
                'setSalt' => '1A2B3C4D5E6F7G890A',
                'setRoles' => array('ROLE_BENEFICIAIRE_DIRECT')
            ],
            'Membre-12' => [
                'setNom' => 'Gervasoni',
                'setUsername' => 'benevole4',
                'setPassword' => 'benevole4',
                'setPrenom' => 'Luigi',
                'setNumeroTel' => '0689785489',
                'setEmail' => 'gervasoni.family@gmail.com',
                'setFonction' => 'Bénévole',
                'setDecideur' => true,
                'setAnnuaire' => true,
                'setSignature' => 'à votre disposition, Luigi Gervasoni',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-3',
                'setSpecialite' => 'Specialite-5',
                'setSalt' => '1A2B3C4D5E6F7G8905',
                'setRoles' => array('ROLE_BENEVOLE')
            ],
            'Membre-13' => [
                'setNom' => 'Vialou',
                'setUsername' => 'beneficiaire5',
                'setPassword' => 'beneficiaire5',
                'setPrenom' => 'Marc',
                'setNumeroTel' => '+33325323456',
                'setEmail' => 'marc-via@gmail.com',
                'setFonction' => 'Gestionnaire',
                'setDecideur' => false,
                'setAnnuaire' => true,
                'setSignature' => 'à votre disposition, Marc Vialou',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-3',
                'setSpecialite' => 'Specialite-4',
                'setSalt' => '1A2B3C4D5E6F7G8901',
                'setRoles' => array('ROLE_BENEFICIAIRE')
            ],
            'Membre-14' => [
                'setNom' => 'Jordana',
                'setUsername' => 'beneficiaire_direct4',
                'setPassword' => 'beneficiaire_direct4',
                'setPrenom' => 'Kamila',
                'setNumeroTel' => '+33325985478',
                'setEmail' => 'kamila_jordana@gmail.com',
                'setFonction' => 'Responsable',
                'setDecideur' => false,
                'setAnnuaire' => true,
                'setSignature' => 'Cordialement, Responsable Kamila Jordana',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-4',
                'setSalt' => '1A2B3C4D5E6F7G890A',
                'setRoles' => array('ROLE_BENEFICIAIRE_DIRECT')
            ],
            'Membre-15' => [
                'setNom' => 'Durand',
                'setUsername' => 'benevole5',
                'setPassword' => 'benevole5',
                'setPrenom' => 'Pierre',
                'setNumeroTel' => '0625455489',
                'setEmail' => 'durand_pierre@gmail.com',
                'setFonction' => 'Bénévole',
                'setDecideur' => true,
                'setAnnuaire' => true,
                'setSignature' => 'à votre disposition, Pierre Durand',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-1',
                'setSpecialite' => 'Specialite-3',
                'setSalt' => '1A2B3C4D5E6F7G8905',
                'setRoles' => array('ROLE_BENEVOLE')
            ],
            'Membre-16' => [
                'setNom' => 'Petrowsky',
                'setUsername' => 'beneficiaire6',
                'setPassword' => 'beneficiaire6',
                'setPrenom' => 'Olaf',
                'setNumeroTel' => '+33312345656',
                'setEmail' => 'olaf_petrowsky@gmail.com',
                'setFonction' => 'Gestionnaire',
                'setDecideur' => false,
                'setAnnuaire' => true,
                'setSignature' => 'à votre disposition, Olaf Petrowsky',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-4',
                'setSalt' => '1A2B3C4D5E6F7G8901',
                'setRoles' => array('ROLE_BENEFICIAIRE')
            ],
            'Membre-17' => [
                'setNom' => 'Petrowsky',
                'setUsername' => 'beneficiaire_direct5',
                'setPassword' => 'beneficiaire_direct5',
                'setPrenom' => 'Olga',
                'setNumeroTel' => '+33412457878',
                'setEmail' => 'olga_petrowsky@gmail.com',
                'setFonction' => 'Responsable',
                'setDecideur' => false,
                'setAnnuaire' => true,
                'setSignature' => 'Cordialement, Responsable Olga Petrowsky',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-3',
                'setSpecialite' => 'Specialite-5',
                'setSalt' => '1A2B3C4D5E6F7G890A',
                'setRoles' => array('ROLE_BENEFICIAIRE_DIRECT')
            ],
            'Membre-18' => [
                'setNom' => 'Fauvet',
                'setUsername' => 'benevole7',
                'setPassword' => 'benevole7',
                'setPrenom' => 'Lionel',
                'setNumeroTel' => '0678547854',
                'setEmail' => 'fauvetlionel@gmail.com',
                'setFonction' => 'Bénévole',
                'setDecideur' => true,
                'setAnnuaire' => true,
                'setSignature' => 'à votre disposition, Lionel Fauvet',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-1',
                'setSpecialite' => 'Specialite-1',
                'setSalt' => '1A2B3C4D5E6F7G8905',
                'setRoles' => array('ROLE_BENEVOLE')
            ],
            'Membre-19' => [
                'setNom' => 'Desidouz',
                'setUsername' => 'beneficiaire7',
                'setPassword' => 'beneficiaire7',
                'setPrenom' => 'Yvonne',
                'setNumeroTel' => '+33325645878',
                'setEmail' => 'yvonne-desidouz@neuf.fr',
                'setFonction' => 'Gestionnaire',
                'setDecideur' => false,
                'setAnnuaire' => true,
                'setSignature' => 'à votre disposition, Yvonne Desidouz',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-3',
                'setSpecialite' => 'Specialite-4',
                'setSalt' => '1A2B3C4D5E6F7G8901',
                'setRoles' => array('ROLE_BENEFICIAIRE')
            ],
            'Membre-20' => [
                'setNom' => 'Ruffier',
                'setUsername' => 'beneficiaire_direct6',
                'setPassword' => 'beneficiaire_direct6',
                'setPrenom' => 'Angelica',
                'setNumeroTel' => '+33457885478',
                'setEmail' => 'ruffier_angelica@gmail.com',
                'setFonction' => 'Responsable',
                'setDecideur' => false,
                'setAnnuaire' => true,
                'setSignature' => 'Cordialement, Responsable Angelica Ruffier',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-4',
                'setSalt' => '1A2B3C4D5E6F7G890A',
                'setRoles' => array('ROLE_BENEFICIAIRE_DIRECT')
            ],
            'Membre-21' => [
                'setNom' => 'Bulstrode',
                'setUsername' => 'benevole6',
                'setPassword' => 'benevole6',
                'setPrenom' => 'Milicent',
                'setNumeroTel' => '0615855489',
                'setEmail' => 'mili_bulstrode@gmail.com',
                'setFonction' => 'Bénévole',
                'setDecideur' => true,
                'setAnnuaire' => true,
                'setSignature' => 'à votre disposition, Milicent Bulstrode',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-1',
                'setSpecialite' => 'Specialite-3',
                'setSalt' => '1A2B3C4D5E6F7G8905',
                'setRoles' => array('ROLE_BENEVOLE')
            ],
            'Membre-22' => [
                'setNom' => 'Deschamps',
                'setUsername' => 'beneficiaire8',
                'setPassword' => 'beneficiaire8',
                'setPrenom' => 'Samuel',
                'setNumeroTel' => '+33321248978',
                'setEmail' => 'deschamps-samuel@neuf.fr',
                'setFonction' => 'Gestionnaire',
                'setDecideur' => false,
                'setAnnuaire' => true,
                'setSignature' => 'à votre disposition, Samuel Deschamps',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-2',
                'setSalt' => '1A2B3C4D5E6F7G8901',
                'setRoles' => array('ROLE_BENEFICIAIRE')
            ],
            'Membre-23' => [
                'setNom' => 'Tilorpat',
                'setUsername' => 'beneficiaire_direct7',
                'setPassword' => 'beneficiaire_direct7',
                'setPrenom' => 'Didier',
                'setNumeroTel' => '+33457885478',
                'setEmail' => 'didier_tilorpat@gmail.com',
                'setFonction' => 'Directeur',
                'setDecideur' => false,
                'setAnnuaire' => true,
                'setSignature' => 'Cordialement, Directeur Didier Tilorpat',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-3',
                'setSpecialite' => 'Specialite-4',
                'setSalt' => '1A2B3C4D5E6F7G890A',
                'setRoles' => array('ROLE_BENEFICIAIRE_DIRECT')
            ],
            'Membre-24' => [
                'setNom' => 'Milot',
                'setUsername' => 'benevole8',
                'setPassword' => 'benevole8',
                'setPrenom' => 'Jordan',
                'setNumeroTel' => '0325458952',
                'setEmail' => 'jordan_milot@gmail.com',
                'setFonction' => 'Bénévole',
                'setDecideur' => true,
                'setAnnuaire' => true,
                'setSignature' => 'à votre disposition, Jordan Milot',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-3',
                'setSpecialite' => 'Specialite-4',
                'setSalt' => '1A2B3C4D5E6F7G8905',
                'setRoles' => array('ROLE_BENEVOLE')
            ],
            'Membre-25' => [
                'setNom' => 'Lautrec',
                'setUsername' => 'beneficiaire9',
                'setPassword' => 'beneficiaire9',
                'setPrenom' => 'Dylan',
                'setNumeroTel' => '+33321154788',
                'setEmail' => 'dylan-lautrec@neuf.fr',
                'setFonction' => 'Gestionnaire',
                'setDecideur' => false,
                'setAnnuaire' => true,
                'setSignature' => 'à votre disposition, Dylan Lautrec',
                'setDisabled' => 0,
                'setEtablissement' => 'Etablissement-1',
                'setSpecialite' => 'Specialite-1',
                'setSalt' => '1A2B3C4D5E6F7G8901',
                'setRoles' => array('ROLE_BENEFICIAIRE')
            ],
        ]
    ];

    public function __construct(ContainerInterface $container = null, UserPasswordEncoderInterface $encoder)
    {
        $this->container = $container;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        // $membresArray = $this->container->getParameter('Membre');
        $membresArray = $this->tabMembres['Membre'];

        foreach ($membresArray as $name => $object) {
            $membre = new Membre();

            foreach ($object as $key => $val) {

                if ($key == 'setEtablissement') {
                    $etablissement = $this->getReference($val);
                    $membre->{$key}($etablissement);
                } elseif ($key == 'setSpecialite') {
                    $specialite = $this->getReference($val);
                    $membre->{$key}($specialite);
                }else if($key == 'setPassword') {
                    $password = $this->encoder($membre, $val);
                    $membre->{$key}($password);
                }
                else {
                    $membre->{$key}($val);
                }
            }

            $manager->persist($membre);
            $this->addReference($name, $membre);
        }
        $manager->flush();
    }
    
    private function encoder(Membre $membre, $val)
    {
        return $this->encoder->encodePassword($membre, $val);
    }

    public function getDependencies()
    {
        return array(
            LoadEtablissementData::class,
            LoadSpecialiteData::class
        );
    }
}