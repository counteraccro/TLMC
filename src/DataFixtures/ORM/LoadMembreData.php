<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Patient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints\DateTime;
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
                'setNom' => 'Admin',
                'setUsername' => 'admin',
                'setPassword' => 'admin',
                'setPrenom' => 'Eric',
                'setNumeroTel' => '+33146589275',
                'setEmail' => 'efonzydu92@gmail.com',
                'setFonction' => 'Directeur',
                'setDecideur' => '1',
                'setAnnuaire' => '0',
                'setSignature' => 'avec tout mon amour, votre cher Eric Fonzy',
                'setDisabled' => '0',
                'setEtablissement' => 'Etablissement-1',
                'setSpecialite' => 'Specialite-1',
                'setSalt' => '1A2B3C4D5E6F7G890',
                'setRoles' => array('ROLE_ADMIN')
            ],
            'Membre-2' => [
                'setNom' => 'Benef',
                'setUsername' => 'beneficiaire',
                'setPassword' => 'beneficiaire',
                'setPrenom' => 'Emilie',
                'setNumeroTel' => '+33113226452',
                'setEmail' => 'emiliejolie@gmail.com',
                'setFonction' => 'Gestionnaire',
                'setDecideur' => '0',
                'setAnnuaire' => '1',
                'setSignature' => 'à votre disposition, Emilie Ratier',
                'setDisabled' => '0',
                'setEtablissement' => 'Etablissement-2',
                'setSalt' => '1A2B3C4D5E6F7G8901',
                'setRoles' => array('ROLE_BENEFICIAIRE')
            ],
            'Membre-3' => [
                'setNom' => 'BenefD',
                'setUsername' => 'beneficiaire_direct',
                'setPassword' => 'beneficiaire_direct',
                'setPrenom' => 'Jean-Michel',
                'setNumeroTel' => '+33146589275',
                'setEmail' => 'efonzydu92@gmail.com',
                'setFonction' => 'Directeur',
                'setDecideur' => '1',
                'setAnnuaire' => '0',
                'setSignature' => 'avec tout mon amour, votre cher Eric Fonzy',
                'setDisabled' => '1',
                'setEtablissement' => 'Etablissement-1',
                'setSpecialite' => 'Specialite-1',
                'setSalt' => '1A2B3C4D5E6F7G890A',
                'setRoles' => array('ROLE_BENEFICIAIRE_DIRECT')
            ],
            'Membre-4' => [
                'setNom' => 'Benevole',
                'setUsername' => 'benevole',
                'setPassword' => 'benevole',
                'setPrenom' => 'Emilie',
                'setNumeroTel' => '+33113226452',
                'setEmail' => 'emiliejolie@gmail.com',
                'setFonction' => 'Bénévole',
                'setDecideur' => '0',
                'setAnnuaire' => '1',
                'setSignature' => 'à votre disposition, Emilie Ratier',
                'setDisabled' => '0',
                'setEtablissement' => 'Etablissement-2',
                'setSalt' => '1A2B3C4D5E6F7G8902',
                'setRoles' => array('ROLE_BENEVOLE')
            ]
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