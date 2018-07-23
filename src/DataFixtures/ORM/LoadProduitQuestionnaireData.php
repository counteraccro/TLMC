<?php
namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\ProduitQuestionnaire;
use App\Entity\Produit;
use App\Entity\Questionnaire;

// use Symfony\Component\DependencyInjection\ContainerInterface;
class LoadProduitQuestionnaireData extends Fixture implements DependentFixtureInterface
{

    private $tabProduitQuestionnaire = [
        'ProduitQuestionnaire' => [
            'ProduitQuestionnaire-1' => [
                'setQuestionnaire' => 'Questionnaire-1',
                'setProduit' => 'Produit-1',
                'setDate' => '2018-07-16'
            ],
            'ProduitQuestionnaire-2' => [
                'setQuestionnaire' => 'Questionnaire-2',
                'setProduit' => 'Produit-2',
                'setDate' => '2018-07-18'
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
        $produitQuestionnaireArray = $this->tabProduitQuestionnaire['ProduitQuestionnaire'];
        foreach ($produitQuestionnaireArray as $name => $value) {
            $produitQuestionnaire = new ProduitQuestionnaire();
            $produit = new Produit();
            $questionnaire = new Questionnaire();

            foreach ($value as $key => $val) {
                if ($key == 'setDate') {
                    $val = new \DateTime($val);
                }

                if ($key == 'setQuestionnaire') {
                    $questionnaire = $this->getReference($val);
                    $produitQuestionnaire->{$key}($questionnaire);
                } elseif ($key == 'setProduit') {
                    $produit = $this->getReference($val);
                    $produitQuestionnaire->{$key}($produit);
                } else {
                    $produitQuestionnaire->{$key}($val);
                }
            }
            $manager->persist($produitQuestionnaire);
            $this->addReference($name, $produitQuestionnaire);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadQuestionnaireData::class,
            LoadProduitData::class
        );
    }
}