<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\ExtensionFormulaire;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Evenement;
use App\Entity\Produit;

class LoadExtensionFormulaireData extends Fixture implements DependentFixtureInterface
{

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    private $tabExtensionFormulaires = [
        'ExtensionFormulaire' => [
            'ExtensionFormulaire-1' => [
                'setEvenement' => 'Evenement-3',
                'setLibelle' => 'Matériel nécessaire',
                'setValeur' => 'Chaussures de sport et des vêtements chauds',
                'setOrdre' => 1,
                'setDisabled' => 0
            ],
            'ExtensionFormulaire-2' => [
                'setProduit' => 'Produit-6',
                'setLibelle' => 'Couleur',
                'setValeur' => 'Marron, Noir',
                'setOrdre' => 1,
                'setDisabled' => 0
            ],
            'ExtensionFormulaire-3' => [
                'setProduit' => 'Produit-6',
                'setLibelle' => 'Matière',
                'setValeur' => 'Plastique ou bois',
                'setOrdre' => 2,
                'setDisabled' => 0
            ]
        ]
    ];

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

		//$extensionFormulairesArray = $this->container->getParameter('ExtensionFormulaire');
		$extensionFormulairesArray = $this->tabExtensionFormulaires['ExtensionFormulaire'];
		
		foreach ($extensionFormulairesArray as $name => $object) {
            $extensionFormulaire = new ExtensionFormulaire();
            $evenement = new Evenement();
            $produit = new Produit();

            foreach ($object as $key => $val) {
                
                if ($key == 'setEvenement') {
                    $evenement = $this->getReference($val);
                    $extensionFormulaire->{$key}($evenement);
                } elseif ($key == 'setProduit') {
                    $produit = $this->getReference($val);
                    $extensionFormulaire->{$key}($produit);
                } else {
                    $extensionFormulaire->{$key}($val);
                }
            }

            $manager->persist($extensionFormulaire);
            $this->addReference($name, $extensionFormulaire);
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadEvenementData::class,
            LoadProduitData::class
        );
    }
}