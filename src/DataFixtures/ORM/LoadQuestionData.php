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
                'setObligatoire' => true,
                'setRegles' => '.',
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
                'setObligatoire' => true,
                'setRegles' => '.',
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
                'setListeValeur' => '{"0":{"value":"0","libelle":"Sarah"},"1":{"value":"1","libelle":"Samuel"},"2":{"value":"2","libelle":"Marcel"},"3":{"value":"3","libelle":"Lara"},"4":{"value":"4","libelle":"Enzo"},"5":{"value":"5","libelle":"Oxana"}}',
                'setDisabled' => '0',
                'setObligatoire' => true,
                'setRegles' => '.',
                'setMessageErreur' => 'Merci d\'indiquer le prénom de l\'accompagnateur que vous préférez',
                'setOrdre' => '1'
            ],
            'Question-4' => [
                'setQuestionnaire' => 'Questionnaire-3',
                'setLibelle' => 'Quel est votre bonbon préféré ?',
                'setLibelleTop' => 'Nous allons vous poser quelques questions sur les bonbons en général',
                'setLibelleBottom' => 'Ceci nous permettra d\'améliorer notre qualité de service à l\'avenir',
                'setType' => 'ChoiceType',
                'setValeurDefaut' => '0',
                'setListeValeur' => '{"0":{"value":"0","libelle":"Choix bonbon"},"1":{"value":"1","libelle":"Crocodiles"},"2":{"value":"2","libelle":"Oursons guimauve"},"3":{"value":"3","libelle":"Fraise Tagada"},"4":{"value":"4","libelle":"Kréma"},"5":{"value":"5","libelle":"Réglisse"}}',
                'setDisabled' => '0',
                'setObligatoire' => true,
                'setRegles' => '.',
                'setMessageErreur' => 'Merci d\'indiquer le bonbon que vous préférez',
                'setOrdre' => '5'
            ],
            'Question-5' => [
                'setQuestionnaire' => 'Questionnaire-3',
                'setLibelle' => 'Dans quel contexte mangez-vous le plus souvent des bonbons ?',
                'setLibelleTop' => '',
                'setLibelleBottom' => 'Ceci nous permettra d\'améliorer notre qualité de service à l\'avenir',
                'setType' => 'TextareaType',
                'setValeurDefaut' => 'Au cinéma, ou dans un lieu de tranquilité',
                'setListeValeur' => '',
                'setDisabled' => '0',
                'setObligatoire' => true,
                'setRegles' => "^[a-zA-Z0-9\sáàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ.;,!?\-']*$",
                'setMessageErreur' => 'Merci d\'indiquer le lieu dans lequel vous mangez le plus souvent des bonbons',
                'setOrdre' => '2'
            ],
            'Question-6' => [
                'setQuestionnaire' => 'Questionnaire-3',
                'setLibelle' => 'Merci de nous indiquer votre marque de bonbons préférée',
                'setLibelleTop' => '',
                'setLibelleBottom' => 'Ceci nous permettra d\'améliorer notre qualité de service à l\'avenir',
                'setType' => 'TextType',
                'setValeurDefaut' => 'Haribo',
                'setListeValeur' => '',
                'setDisabled' => '0',
                'setObligatoire' => true,
                'setRegles' => "^[a-zA-Z\sáàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ.;,!?\-']*$",
                'setMessageErreur' => 'Merci d\'indiquer votre marque de bonbons préférée en lettre uniquement',
                'setOrdre' => '3'
            ],
            'Question-7' => [
                'setQuestionnaire' => 'Questionnaire-3',
                'setLibelle' => 'Quels bonbons énumérés ci-dessous avez-vous déjà goûté ?',
                'setLibelleTop' => 'Nous allons maintenant vous posez des questions sur vos goûts',
                'setLibelleBottom' => 'Ceci nous permettra d\'améliorer notre qualité de service à l\'avenir',
                'setType' => 'CheckboxType',
                'setValeurDefaut' => '',
                'setListeValeur' => '{"0":{"value":"0","libelle":"Bananes"},"1":{"value":"1","libelle":"Crocodiles"},"2":{"value":"2","libelle":"Oursons guimauve"},"3":{"value":"3","libelle":"Fraise Tagada"},"4":{"value":"4","libelle":"Kréma"},"5":{"value":"5","libelle":"Réglisse"}}',
                'setDisabled' => '0',
                'setObligatoire' => true,
                'setRegles' => '.',
                'setMessageErreur' => 'Merci d\'indiquer les bonbons que vous avez déjà goûtés',
                'setOrdre' => '4'
            ],
            'Question-8' => [
                'setQuestionnaire' => 'Questionnaire-3',
                'setLibelle' => 'Combien de fois par mois mangez-vous des bonbons ?',
                'setLibelleTop' => '',
                'setLibelleBottom' => 'Ceci nous permettra d\'améliorer notre qualité de service à l\'avenir',
                'setType' => 'RadioType',
                'setValeurDefaut' => '',
                'setListeValeur' => '{"0":{"value":"0","libelle":"0"},"1":{"value":"1","libelle":"1"},"2":{"value":"2","libelle":"2"},"3":{"value":"3","libelle":"3"},"4":{"value":"4","libelle":"4"},"5":{"value":"5","libelle":"5"}}',
                'setDisabled' => '0',
                'setObligatoire' => true,
                'setRegles' => '.',
                'setMessageErreur' => 'Merci d\'indiquer votre fréquence de consommation de bonbons',
                'setOrdre' => '1'
            ],
            'Question-9' => [
                'setQuestionnaire' => 'Questionnaire-4',
                'setLibelle' => 'Comment avez-vous eu connaissance de l\'association ?',
                'setLibelleTop' => 'Nous allons dès maintenant vous poser quelques questions pour recueillir votre avis concernant le fonctionnement de l’association. ',
                'setLibelleBottom' => 'Pour les plus jeunes, l’aide des parents est la bienvenue pour remplir ce questionnaire',
                'setType' => 'RadioType',
                'setValeurDefaut' => '',
                'setListeValeur' => '{"0":{"value":"0","libelle":"en Mairie"},"1":{"value":"1","libelle":"par le bouche-à-oreille"},"2":{"value":"2","libelle":"par l\'école ou le collège"},"3":{"value":"3","libelle":"flyers"},"4":{"value":"4","libelle":"campagne de sensibilisation"}}',
                'setDisabled' => '0',
                'setObligatoire' => true,
                'setRegles' => '.',
                'setMessageErreur' => 'Merci d\'indiquer le canal principal par lequel vous nous avez connu',
                'setOrdre' => '1'
            ],
            'Question-10' => [
                'setQuestionnaire' => 'Questionnaire-4',
                'setLibelle' => 'Vous êtes-vous déjà impliqué dans la vie de l’association en : ',
                'setLibelleBottom' => 'Ceci nous permettra de mieux identifier la répartition des rôles dans l\'association',
                'setType' => 'CheckboxType',
                'setValeurDefaut' => '',
                'setListeValeur' => '{"0":{"value":"0","libelle":"participant à l’Assemblée Générale"},"1":{"value":"1","libelle":"participant aux réunions du Conseil d’Administration"},"2":{"value":"2","libelle":"participant à un comité de suivi ou un groupe de travail"},"3":{"value":"3","libelle":"en tant que bénévole dans une activité spécifique"},"4":{"value":"4","libelle":"non, car je manque de temps"},"5":{"value":"5","libelle":"non, je ne connaissais pas vraiment les possibilités"}}',
                'setDisabled' => '0',
                'setObligatoire' => true,
                'setRegles' => '.',
                'setMessageErreur' => 'Merci d\'indiquer les fonctions que vous avez déjà occupées',
                'setOrdre' => '2'
            ],
            'Question-11' => [
                'setQuestionnaire' => 'Questionnaire-4',
                'setLibelle' => 'S\'il y avait des améliorations à apporter, que proposeriez-vous ? ',
                'setLibelleTop' => 'Pouvons-nous en faire davantage ? nous améliorer ?',
                'setLibelleBottom' => 'N\'hésitez pas à nous faire part de toutes vos remarques.',
                'setType' => 'TextareaType',
                'setValeurDefaut' => 'Vos suggestions peuvent porter sur l\'organisation, le matériel...',
                'setListeValeur' => '',
                'setDisabled' => '0',
                'setObligatoire' => true,
                'setRegles' => '.',
                'setMessageErreur' => 'Merci d\'indiquer les améliorations possibles',
                'setOrdre' => '3'
            ],       
            'Question-12' => [
                'setQuestionnaire' => 'Questionnaire-4',
                'setLibelle' => 'Votez pour la prochaine activité mise en place par l\'association :',
                'setLibelleBottom' => 'N\'hésitez pas à nous faire part de toute nouvelle idée d\'activité par e-mail.',
                'setType' => 'ChoiceType',
                'setValeurDefaut' => '0',
                'setListeValeur' => '{"0":{"value":"0","libelle":"Natation"},"1":{"value":"1","libelle":"Escrime"},"2":{"value":"2","libelle":"Tennis"},"3":{"value":"3","libelle":"Yoga"},"4":{"value":"4","libelle":"Tournoi d\'échecs"},"5":{"value":"5","libelle":"Balades à poney"}}',
                'setDisabled' => '0',
                'setObligatoire' => true,
                'setRegles' => '.',
                'setMessageErreur' => 'Merci d\'indiquer votre vote concernant l\'activité à développer',
                'setOrdre' => '4'
            ],
            'Question-13' => [
                'setQuestionnaire' => 'Questionnaire-4',
                'setLibelle' => 'Vous pouvez nous suggérer un lieu pour notre prochaine nouvelle activité :',
                'setLibelleBottom' => 'Cette question est facultative.',
                'setType' => 'TextType',
                'setValeurDefaut' => 'Gymnase Bombardier',
                'setDisabled' => '0',
                'setObligatoire' => false,
                'setRegles' => '.',
                'setMessageErreur' => 'Merci d\'indiquer votre réponse en lettres',
                'setOrdre' => '5'
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