<?php
/**
 * Traitement des booléens dans twig
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Entity\Questionnaire;
use App\Entity\Question;
use Symfony\Component\Config\Definition\Exception\Exception;

class QuestionnaireExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return array(
            new TwigFunction('questionnaire', array(
                $this,
                'questionnaire'
            ))
        );
    }
    
    //permet de déterminer s'il s'agit d'un url prod ou de démo
    const DEMO = 'demo';
    const PROD = 'prod';
    
    private $params = [
        'url' => '',
        'statut' => self::PROD,
    ];

    /**
     *Fonction permettant d'identifier le type de question envoyée et de retourner un format en conséquence
     * @param Questionnaire $questionnaire
     * @return string
     */
    public function questionnaire(Questionnaire $questionnaire, $params)
    {
        $this->checkTabParams($params);
        
        $html = '';
        $html .= $this->infoBloc();
        $html .= $this->beginForm();

        /* @var Question $question */
        foreach ($questionnaire->getQuestions() as $question) {
            
            switch ($question->getType()) {
                case 'ChoiceType':
                    $html .= $this->ChoiceType($question);
                    break;
                case 'TextType':
                    $html .= $this->TextType($question);
                    break;
                case 'TextareaType':
                    $html .= $this->TextAreaType($question);
                    break;
                case 'CheckboxType':
                    $html .= $this->CheckboxType($question);
                    break;
                case 'RadioType':
                    $html .= $this->RadioType($question);
                    break;
                default:
                    $html .= '<div class="alert alert-danger">Le type ' . $question->getType() . ' n\'existe pas</div>';
                    break;
            }
        }

        $html .= $this->endForm();

        return $html;
    }

    /**
     * Fonction prévue dans le cas où la question comprend une liste déroulante
     * @param Question $question
     */
    private function ChoiceType(Question $question)
    {
        $html = '';
        $html = $this->beginBloc($question);

        $html .= '<label for="q-' . $question->getId() . '">' . $question->getLibelle() . " - " . "ID # " . $question->getId() . '</label>
                    <select class="form-control" id="q-' . $question->getId() . '" name="questionnaire[question][q-' . $question->getId() . ']">';

        $data_value = json_decode($question->getListeValeur());
        foreach ($data_value as $val) {
            $selected = '';
            if ($val->value == $question->getValeurDefaut()) {
                $selected = ' selected';
            }

            $html .= '<option value="' . $val->value . '"' . $selected . '>' . $val->libelle . '</option>';
        }
        $html .= '</select>';

        $html .= $this->endBloc($question);

        return $html;
    }

    /**
     * Fonction prévue dans le cas où la question comprend un champ texte
     * @param Question $question
     */
    private function TextType(Question $question)
    {
        $html = '';
        $html = $this->beginBloc($question);

        $html .= '<label for="q-' . $question->getId() . '">' . $question->getLibelle() . " - " . "ID # " . $question->getId() . '</label>
                    <input class="form-control" id="q-' . $question->getId() . '" name="questionnaire[question][q-' . $question->getId() . ']">';
        
        $html .= $this->endBloc($question);

        return $html;
    }

    /**
     * Fonction prévue dans le cas où la question comprend une zone de texte
     * @param Question $question
     */
    private function TextAreaType(Question $question)
    {
        $html = '';
        $html = $this->beginBloc($question);

        $html .= '<label for="q-' . $question->getId() . '">' . $question->getLibelle() . " - " . "ID # " . $question->getId() . '</label>
                    <textarea  class="form-control" id="q-' . $question->getId() . '" name="questionnaire[question][q-' . $question->getId() . ']">';
        $html .= '</textarea>';

        $html .= $this->endBloc($question);

        return $html;
    }

    /**
     * Fonction prévue dans le cas où la question comprend une liste de réponses en format cases à cocher
     * @param Question $question
     */
    private function CheckboxType(Question $question)
    {
        $html = '';
        $html = $this->beginBloc($question);

        $html .= '<label for="q-' . $question->getId() . '">' . $question->getLibelle() . " - " . "ID # " . $question->getId() . '</label>
          <div class="form-check">';

        $data_value = json_decode($question->getListeValeur());
        foreach ($data_value as $val) {

            $checked = '';
            if ($val->value == $question->getValeurDefaut()) {
                $checked = ' checked';
            }

            $html .= '<input class="form-check-input" type="checkbox" value="' . $val->value . '"' . $checked . '" id="q-' . $question->getId() . '" name="questionnaire[question][q-' . $question->getId() . ']">';
            $html .= '<label class="form-check-label" for="q-' . $question->getId() . '">' . $val->libelle . '</label><br>';
        }

        $html .= '</div>';

        $html .= $this->endBloc($question);

        return $html;
    }

    /**
     * Fonction prévue dans le cas où la question comprend une liste de réponses au format radio (choix unique)
     * @param Question $question
     */
    private function RadioType(Question $question)
    {
        $html = '';
        $html = $this->beginBloc($question);

        $html .= '<label for="q-' . $question->getId() . '">' . $question->getLibelle() . " - " . "ID # " . $question->getId() . '</label>
          <div class="form-check">';

        $data_value = json_decode($question->getListeValeur());
        foreach ($data_value as $val) {
            $checked = '';
            if ($val->value == $question->getValeurDefaut()) {
                $checked = ' checked';
            }

            $html .= '<input class="form-check-input" type="radio" value="' . $val->value . '"' . $checked . '" id="q-' . $question->getId() . '" name="questionnaire[question][q-' . $question->getId() . ']">';
            $html .= '<label class="form-check-label" for="q-' . $question->getId() . '">' . $val->value . '</label><br>';
        }

        $html .= '</div>';
        $html .= $this->endBloc($question);

        return $html;
    }

    /**
     * Début HTML du questionnaire
     * @return string
     */
    private function beginForm()
    {
        $html = "";
        $html .= '<form action="' . $this->params['url'] . '" method="post" class="">';
        return $html;
    }

    /**
     * Fin HTML du questionnaire
     * @return string
     */
    private function endForm()
    {
        $html = "";
        $html .= '<br /><button type="submit" name="validation" class="btn btn-primary mb-2">Valider</button>';
        $html .= "</form>";
        return $html;
    }

    /**
     * Création du début du bloc pour chaque question
     * @param Question $question
     */
    private function beginBloc(Question $question)
    {
        $html = '';
        $html .= '<div id="bloc-' . $question->getId() . '" class="card">';

        if (! empty($question->getLibelleTop())) {
            $html .= '<div class="card-header">' . $question->getLibelleTop() . '</div>';
        }

        $html .= '<div class="card-body"><div class="form-group">';
        return $html;
    }

    /**
     * Création de la fin du bloc pour chaque question
     * @param Question $question
     */
    private function endBloc(Question $question)
    {
        $html = '';
        if (! empty($question->getLibelleBottom())) {
            $html .= '<small id="help-q-' . $question->getId() . '" class="form-text text-muted">' . $question->getLibelleBottom() . '</small>';
        }

        // Fin form-group, fin bloc-[id]
        $html .= '</div></div></div><br />';
        return $html;
    }
    
    /**
     * Bloc permettant d'insérer un message d'information à l'utilisateur
     * @return string
     */
    private function infoBloc()
    {
        $html = '';
        
        if($this->params['statut'] == self::DEMO)
        {
            $html .= '<div class="alert alert-info">Vous êtes actuellement en démo, aucune donnée saisie ne sera prise en compte.</div>';
        }
        
        return $html;
    }
    
    /**
     * Vérifie si les paramètres sont corrects
     * @param array $params
     * @throws Exception
     */
    private function checkTabParams($params)
    {
        foreach($params as $key => $value)
        {
            if(isset($this->params[$key]))
            {
                $this->params[$key] = $value;
            }
            else
            {
                $str = '';
                foreach($this->params as $pkey => $pvalue)
                {
                    $str .= $pkey . ', ';
                }
                $str = substr($str, 0, -2);
                
                throw new Exception('Le paramètre ' . $key . ' n\'existe pas, paramètres attendus : ' . $str);
            }
        }
    }
}
