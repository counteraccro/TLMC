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
                'setEvenement' => 'Evenement-1',
                'setCle' => '64f48f45sef8f4e',
                'setValeur' => 'joji',
                'setType' => 'input',
                'setValeurDefaut' => 'Joji123',
                'setListeValeur' => '{"coucou","c\'est","moi"}',
                'setObligatoire' => '1',
                'setDisabled' => '1',
                'setOrdre' => '1',
                'setRegles' => '{"voila"}'
            ],
            'ExtensionFormulaire-2' => [
                'setProduit' => 'Produit-1',
                'setCle' => '948drg4t4h9rt4h',
                'setValeur' => '456',
                'setType' => 'Textarea',
                'setValeurDefaut' => '2',
                'setListeValeur' => 'Paris',
                'setObligatoire' => '0',
                'setDisabled' => '0',
                'setOrdre' => '2',
                'setRegles' => '{"voici"}'
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