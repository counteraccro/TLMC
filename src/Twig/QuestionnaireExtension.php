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

    // permet de déterminer s'il s'agit d'un statut prod ou de démo
    const DEMO = 'demo';
    const PROD = 'prod';
    const EDIT = 'edit';

    private $params = [
        'submit_url' => '',
        'edit_url' => '',
        'statut' => self::PROD,
        'reponses' => array(), // Donnée retournée après le submit dans le $_POST
        'errors' => array()
    ];

    /**
     * Fonction permettant d'identifier le type de question envoyée et de retourner un format en conséquence
     *
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
     *
     * @param Question $question
     */
    private function ChoiceType(Question $question)
    {
        $html = '';
        $html = $this->beginBloc($question);

        $html .= '<label for="q-' . $question->getId() . '">' . $question->getLibelle() . '</label>
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
     *
     * @param Question $question
     */
    private function TextType(Question $question)
    {
        $html = '';
        $html = $this->beginBloc($question);

        $html .= '<label for="q-' . $question->getId() . '">' . $question->getLibelle() . '</label>
                    <input class="form-control" placeholder="' . $question->getValeurDefaut() . '" id="q-' . $question->getId() . '" name="questionnaire[question][q-' . $question->getId() . ']" />';

        $html .= $this->endBloc($question);

        return $html;
    }

    /**
     * Fonction prévue dans le cas où la question comprend une zone de texte
     *
     * @param Question $question
     */
    private function TextAreaType(Question $question)
    {
        $html = '';
        $html = $this->beginBloc($question);

        $html .= '<label for="q-' . $question->getId() . '">' . $question->getLibelle() . '</label>
                    <textarea  class="form-control" placeholder="' . $question->getValeurDefaut() . '" id="q-' . $question->getId() . '" name="questionnaire[question][q-' . $question->getId() . ']">';
        $html .= '</textarea>';

        $html .= $this->endBloc($question);

        return $html;
    }

    /**
     * Fonction prévue dans le cas où la question comprend une liste de réponses en format cases à cocher
     *
     * @param Question $question
     */
    private function CheckboxType(Question $question)
    {
        $html = '';
        $html = $this->beginBloc($question);

        $html .= '<label for="q-' . $question->getId() . '">' . $question->getLibelle() . '</label>
          <div class="form-check">';

        $data_value = json_decode($question->getListeValeur());
        $i = 0;
        foreach ($data_value as $val) {

            $checked = '';
            if ($val->value == $question->getValeurDefaut()) {
                $checked = ' checked';
            }

            $html .= '<input class="form-check-input" type="checkbox" value="' . $val->value . '"' . $checked . '" id="q-' . $question->getId() . '-' . $i . '" name="questionnaire[question][q-' . $question->getId() . '][' . $i . ']">';
            $html .= '<label class="form-check-label" for="q-' . $question->getId() . '-' . $i . '">' . $val->libelle . '</label><br>';
            $i++;
        }

        $html .= '</div>';

        $html .= $this->endBloc($question);

        return $html;
    }

    /**
     * Fonction prévue dans le cas où la question comprend une liste de réponses au format radio (choix unique)
     *
     * @param Question $question
     */
    private function RadioType(Question $question)
    {
        $html = '';
        $html = $this->beginBloc($question);

        $html .= '<label for="q-' . $question->getId() . '">' . $question->getLibelle() . '</label>
          <div class="form-check">';

        $data_value = json_decode($question->getListeValeur());
        
        $i = 0;
        foreach ($data_value as $val) {
            $checked = '';
            if ($val->value == $question->getValeurDefaut()) {
                $checked = ' checked';
            }

            $html .= '<input class="form-check-input" type="radio" value="' . $val->value . '"' . $checked . '" id="q-' . $question->getId() . '-' . $i . '" name="questionnaire[question][q-' . $question->getId() . ']">';
            $html .= '<label class="form-check-label" for="q-' . $question->getId() . '-' . $i . '">' . $val->value . '</label><br>';
            $i++;
        }

        $html .= '</div>';
        $html .= $this->endBloc($question);

        return $html;
    }

    /**
     * Début HTML du questionnaire
     *
     * @return string
     */
    private function beginForm()
    {
        $html = "";
        $html .= '<form action="' . $this->params['submit_url'] . '" method="post" class="">';
        return $html;
    }

    /**
     * Fin HTML du questionnaire
     *
     * @return string
     */
    private function endForm()
    {
        $html = "";
        if($this->params['statut'] != self::EDIT)
        {
            $html .= '<br /><button type="submit" name="validation" class="btn btn-primary mb-2">Valider</button>';
        }
        $html .= "</form>";
        return $html;
    }

    /**
     * Création du début du bloc pour chaque question
     *
     * @param Question $question
     */
    private function beginBloc(Question $question)
    {
        
        $html = '';
        $html .= '<div id="bloc-' . $question->getId() . '" class="card">';

        $txt_id = '';
        if ($this->params['statut'] == self::DEMO || $this->params['statut'] == self::EDIT) {
            $txt_id = "#" . $question->getId();
        }
        
        $edit = '';
        if ($this->params['statut'] == self::EDIT) {
            
            $edit_url = str_replace('/0', '/' . $question->getId(), $this->params['edit_url']);
            
            $edit = '<div class="float-right">
                <a href="' . $edit_url . '" id="btn-edit-question" class="btn btn-primary btn-edit-question"><span class="oi oi-pencil"></span> Edition</a>
            </div>';
        }
            
        if (!empty($question->getLibelleTop())) {
            $html .= '<div class="card-header">' . $txt_id . ' - '. $question->getLibelleTop() . $edit . '</div>';
        }
        else if ($this->params['statut'] == self::EDIT) {
            $html .= '<div class="card-header">' . $txt_id . $edit . '</div>';
        }
        else if ($this->params['statut'] == self::DEMO) {
            $html .= '<div class="card-header">' . $txt_id . '</div>';
        }
        
        $html .= '<div class="card-body"><div class="form-group">';
        return $html;
    }

    /**
     * Création de la fin du bloc pour chaque question
     *
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
     *
     * @return string
     */
    private function infoBloc()
    {
        $html = '';

        if ($this->params['statut'] == self::DEMO) {
            $html .= '<div class="alert alert-info">Vous êtes actuellement en démo, aucune donnée saisie ne sera prise en compte.</div>';
        }

        return $html;
    }

    /**
     * Vérifie si les paramètres sont corrects
     *
     * @param array $params
     * @throws Exception
     */
    private function checkTabParams($params)
    {
        foreach ($params as $key => $value) {
            if (isset($this->params[$key])) {
                $this->params[$key] = $value;
            } else {
                $str = '';
                foreach ($this->params as $pkey => $pvalue) {
                    $str .= $pkey . ', ';
                }
                $str = substr($str, 0, - 2);

                throw new Exception('Le paramètre ' . $key . ' n\'existe pas, paramètres attendus : ' . $str);
            }
        }
    }
}
