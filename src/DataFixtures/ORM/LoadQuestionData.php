<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LoadQuestionData extends Fixture implements DependentFixtureInterface
{

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    private $tabQuestions = [
        'Question' => [
            'Question-1' => [
                'setQuestionnaire' => 'Questionnaire-1',
                'setLibelle' => 'Quel moyen de transport avez vous utilisé pour venir ?',
                'setLibelleTop' => 'A partir de maintenant nous allons vous poser des questions sur vos modes de transports...',
                'setLibelleBottom' => 'Il peut être public ou privé',
                'setType' => 'ChoiceType',
                'setValeurDefaut' => 'Vélo',
                'setListeValeur' => '{"0":{"value":"0","libelle":"Choix transport"},"1":{"value":"2","libelle":"Bus"},"2":{"value":"3","libelle":"Trains"},"3":{"value":"4","libelle":"Vélo"},"4":{"value":"5","libelle":"A pied"},"5":{"value":"6","libelle":"Voiture"}}',
                'setDisabled' => '0',
                'setObligatoire' => 'true',
                'setRegles' => 'regles json',
                'setMessageErreur' => 'Veuillez choisir un mode de transport',
                'setOrdre' => '1'
            ],
            'Question-2' => [
                //'setQuestion' => 'Question-1',
                'setQuestionnaire' => 'Questionnaire-1',
                'setLibelle' => 'Que pensez-vous de la voiture électrique ?',
                'setLibelleTop' => 'Evoquons désormais des modes de transports plus modernes/écologiques',
                'setLibelleBottom' => 'Un véhicule plus \'vert\' contribue à la pérennisation de la planète',
                'setType' => 'TextareaType',
                'setValeurDefaut' => 'Je n\'ai pas d\'avis sur la question',
                'setListeValeur' => '',
                'setDisabled' => '0',
                'setObligatoire' => 'true',
                'setRegles' => 'regles json',
                'setMessageErreur' => 'Merci de nous donner votre opinion concernant la question posée ci-dessus',
                'setOrdre' => '2'
            ],
            'Question-3' => [
                'setQuestionnaire' => 'Questionnaire-2',
                'setLibelle' => 'Quel est le prénom de l\'accompagnateur que vous préférez ?',
                'setLibelleTop' => 'Nous allons vous poser quelques questions sur l\'encadrement des évènements',
                'setLibelleBottom' => 'Ceci nous permettra d\'améliorer notre qualité de service à l\'avenir',
                'setType' => 'RadioType',
                'setValeurDefaut' => 'Marcel',
                'setListeValeur' => '{"0":{"value":"0","libelle":"Accompagnateur favori"},"1":{"value":"2","libelle":"Samuel"},"2":{"value":"3","libelle":"Marcel"},"3":{"value":"4","libelle":"Lara"},"4":{"value":"5","libelle":"Enzo"},"5":{"value":"6","libelle":"Oxana"}}',
                'setDisabled' => '0',
                'setObligatoire' => 'true',
                'setRegles' => 'regles json',
                'setMessageErreur' => 'Merci d\'indiquer le prénom de l\'accompagnateur que vous préférez',
                'setOrdre' => '1'
            ]
        ]
    ];

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        // $questionsArray = $this->container->getParameter('Question');
        $questionsArray = $this->tabQuestions['Question'];

        foreach ($questionsArray as $name => $object) {
            $question = new Question();

            foreach ($object as $key => $val) {
                
                switch ($key) {
                    case 'setQuestionnaire':
                    case 'setQuestion':
                        $val = $this->getReference($val);
                        break;
                }
                
                $question->{$key}($val);
            }

            $manager->persist($question);
            $this->addReference($name, $question);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadQuestionnaireData::class
        );
    }
}