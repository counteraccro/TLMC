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
use App\Controller\AppController;
use App\Entity\Reponse;
use Doctrine\ORM\Tools\Pagination\Paginator;

class QuestionnaireExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('questionnaire', array(
                $this,
                'questionnaire'
            )),
            new TwigFunction('displayDataToSave', array(
                $this,
                'displayDataToSave'
            )),
            new TwigFunction('displayMembreWithReponse', array(
                $this,
                'displayMembreWithReponse'
            ))
        );
    }

    // permet de déterminer s'il s'agit d'un statut prod ou de démo
    const DEMO = AppController::DEMO;

    const PROD = AppController::PROD;

    const EDIT = AppController::EDIT;

    private $params = [
        'current_user' => '',
        'submit_url' => '',
        'edit_url' => '',
        'statut' => self::PROD,
        'resultat' => array()
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

        if ($questionnaire->getQuestions()->isEmpty()) {
            return '<div class="text-center text-info">Aucune question n\'est associée à ce questionnaire</div>';
        }

        $html .= $this->infoBloc();
        $html .= $this->beginForm();

        /* @var Question $question */
        foreach ($questionnaire->getQuestions() as $question) {

            if ($question->getDisabled() && $this->params['statut'] != self::EDIT) {
                continue;
            }

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

            $html = $this->isObligatoire($question, $html);
        }

        $html .= $this->endForm($questionnaire);

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
        
        $html .= $this->generateChartStats($question);

        $html .= '<label for="q-' . $question->getId() . '">' . $question->getLibelle() . '%o%</label>
                    <select class="form-control ' . $this->isValidateError($question) . '" id="q-' . $question->getId() . '" name="questionnaire[question][q-' . $question->getId() . ']">';

        $data_value = json_decode($question->getListeValeur());
        foreach ($data_value as $val) {
            $selected = '';

            if (isset($this->params['resultat'][$question->getId()])) {
                $reponse = $this->params['resultat'][$question->getId()]->reponse;
                if ($reponse->getValeur() == $val->value) {
                    $selected = ' selected';
                }
            } else if ($val->value == $question->getValeurDefaut()) {
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

        $strReponse = '';
        if (isset($this->params['resultat'][$question->getId()])) {
            $reponse = $this->params['resultat'][$question->getId()]->reponse;
            $strReponse = 'value="' . $reponse->getValeur() . '"';
        }

        $html .= '<label for="q-' . $question->getId() . '">' . $question->getLibelle() . '%o%</label>
                    <input type="text" class="form-control ' . $this->isValidateError($question) . '" ' . $strReponse . ' placeholder="' . $question->getValeurDefaut() . '" id="q-' . $question->getId() . '" name="questionnaire[question][q-' . $question->getId() . ']" />';

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

        $strReponse = '';
        if (isset($this->params['resultat'][$question->getId()])) {
            $reponse = $this->params['resultat'][$question->getId()]->reponse;
            $strReponse = $reponse->getValeur();
        }

        $html .= '<label for="q-' . $question->getId() . '">' . $question->getLibelle() . '%o%</label>
                    <textarea  class="form-control ' . $this->isValidateError($question) . '" placeholder="' . $question->getValeurDefaut() . '" id="q-' . $question->getId() . '" name="questionnaire[question][q-' . $question->getId() . ']">';
        $html .= $strReponse;
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
        
        $html .= $this->generateChartStats($question);

        $html .= '<label for="q-' . $question->getId() . '">' . $question->getLibelle() . '%o%</label>
          <div class="form-check">';

        $data_value = json_decode($question->getListeValeur());
        $i = 0;
        foreach ($data_value as $val) {

            $checked = '';
            if (isset($this->params['resultat'][$question->getId()])) {
                $reponse = $this->params['resultat'][$question->getId()]->reponse;

                $tmp = explode('|', $reponse->getValeur());

                foreach ($tmp as $tmpVal) {
                    if ($val->value == $tmpVal) {
                        $checked = ' checked';
                    }
                }
            } else if ($val->value == $question->getValeurDefaut()) {
                $checked = ' checked';
            }

            $html .= '<input class="form-check-input ' . $this->isValidateError($question) . '" type="checkbox" value="' . $val->value . '"' . $checked . ' id="q-' . $question->getId() . '-' . $i . '" name="questionnaire[question][q-' . $question->getId() . '][' . $i . ']">';
            $html .= '<label class="form-check-label" for="q-' . $question->getId() . '-' . $i . '">' . $val->libelle . '</label><br>';
            $i ++;
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
        
        $html .= $this->generateChartStats($question);

        $html .= '<label for="q-' . $question->getId() . '">' . $question->getLibelle() . '%o%</label>
          <div class="form-check">';

        $data_value = json_decode($question->getListeValeur());

        $i = 0;
        foreach ($data_value as $val) {
            $checked = '';

            if (isset($this->params['resultat'][$question->getId()])) {
                $reponse = $this->params['resultat'][$question->getId()]->reponse;

                if ($val->value == $reponse->getValeur()) {
                    $checked = ' checked';
                }
            } else if ($val->value == $question->getValeurDefaut()) {
                $checked = ' checked';
            }

            $html .= '<input class="form-check-input ' . $this->isValidateError($question) . '" type="radio" value="' . $val->value . '"' . $checked . ' id="q-' . $question->getId() . '-' . $i . '" name="questionnaire[question][q-' . $question->getId() . ']">';
            $html .= '<label class="form-check-label" for="q-' . $question->getId() . '-' . $i . '">' . $val->libelle . '</label><br>';
            $i ++;
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
    private function endForm(Questionnaire $questionnaire)
    {
        $html = "";
        if ($this->params['statut'] != self::EDIT) {
            $html .= '<br /><button type="submit" name="validation" class="btn btn-primary mb-2" id="btn-valide-' . $questionnaire->getId() . '">Valider</button>';

            $html .= "<script>
                $('#btn-valide-" . $questionnaire->getId() . "').click(function() {
                    $(this).addClass('disabled').html('Loading...');
                });
            </script>";
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
            $txt_id = "#" . $question->getId() . '-';
        }

        $edit = '';
        if ($this->params['statut'] == self::EDIT) {

            $edit_url = str_replace('/0', '/' . $question->getId(), $this->params['edit_url']);

            $disabled = '';
            if ($question->getDisabled()) {
                $disabled = '<i class="text-secondary"><span class="oi oi-info"></span> Cette question est désactivée</i> &nbsp;&nbsp;&nbsp;';
            }

            $btn_disabled = '';
            if ((new \DateTime('now') > $question->getQuestionnaire()->getDateFin() || $question->getQuestionnaire()->getPublication()) || $this->checkQuestionHaveOneReponse($question)) {
                $btn_disabled = 'disabled';
            }

            if ($this->params['current_user']->getRoles()[0] == 'ROLE_ADMIN') {
                $edit = '<div class="float-right">
                    ' . $disabled . '
                    <a href="' . $edit_url . '" id="btn-edit-question" class="btn btn-primary btn-edit-question ' . $btn_disabled . '"><span class="oi oi-pencil"></span> Edition</a>
                </div>';
            }
        }

        if (! empty($question->getLibelleTop())) {
            $html .= '<div class="card-header">' . $txt_id . ' ' . $question->getLibelleTop() . $edit . '</div>';
        } else if ($this->params['statut'] == self::EDIT) {
            $html .= '<div class="card-header">' . $txt_id . $edit . '</div>';
        } else if ($this->params['statut'] == self::DEMO) {
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

        if (isset($this->params['resultat'][$question->getId()])) {
            if ($this->params['resultat'][$question->getId()]->erreur->is) {
                $html .= '<div class="invalid-feedback">
                    ' . $this->params['resultat'][$question->getId()]->erreur->libelle . ' 
                </div>';
                $html .= "<script>$('#bloc-" . $question->getId() . " .invalid-feedback').show();</script>";
            }
        }

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
        $html .= '<small class="text-danger"><i>Les champs marqués d\'une * sont obligatoires</i></small>';

        return $html;
    }

    /**
     * Permet d'ajouter le marqueur étoile signifiant que la réponse à cette question est requise
     *
     * @param Question $question
     * @param string $html
     */
    private function isObligatoire(Question $question, $html)
    {
        if ($question->getObligatoire() === true) {
            return str_replace('%o%', ' <span class="text-danger">*</span>', $html);
        } else {
            return str_replace('%o%', '', $html);
        }
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

    /**
     * Vérifie si la question comporte au moins 1 réponse
     *
     * @param Question $question
     * @return boolean
     */
    private function checkQuestionHaveOneReponse(Question $question)
    {
        if ($question->getReponses()->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Vérifie si le champ est en erreur ou non
     *
     * @param Question $question
     * @return string ou '';
     */
    private function isValidateError(Question $question)
    {
        $erreur = '';
        if (isset($this->params['resultat'][$question->getId()])) {
            if ($this->params['resultat'][$question->getId()]->erreur->is) {
                $erreur = 'is-invalid';
            }
        }

        return $erreur;
    }
    
    private function generateChartStats(Question $question)
    {
        $html = '';
        
        $html .= '
            <div class="float-right google-chart" id="donutchart-' . $question->getId() . '"></div>
        ';
        
        $data_value = json_decode($question->getListeValeur());
        $data = '';
        foreach ($data_value as $val) {
            $data .= "['" . $val->libelle . "', 1],";
        }
        
        $data = substr($data, 0, -1);
        
        $js = "<script>
        google.charts.load('current', {packages:['corechart']});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
          var data = google.visualization.arrayToDataTable([
            ['Task', 'bla'],
            " . $data . "
          ]);
    
          var options = {
            pieHole: 0.2,
            chartArea: { 'width' : '100%', 'height' : '100%' } 
          };
    
          var chart = new google.visualization.PieChart(document.getElementById('donutchart-" . $question->getId() . "'));
          chart.draw(data, options);
        }
        </script>";
        
        $html .= $js;
        
        return $html;
    }

    /**
     * Permet de visualiser des réponses saisies au questionnaire de démo (mode debug)
     * Prévoit l'affichage des erreurs (obligatoire, non-respect des règles fixées)
     *
     * @param array $result
     * @return string
     */
    public function displayDataToSave(array $result)
    {
        if (empty($result)) {
            $html = '<div class="alert alert-primary" role="alert">
                        Il faut soumettre le formulaire de démo pour voir les données qui seront enregistrées
                    </div>';
            return '<div class="card "><div class="card-body">' . $html . '</div></div>';
        }

        $html = '';
        $is_error = false;

        foreach ($result as $key => $object) {
            /* @var Question $question */
            $question = $object->question;

            /* @var Reponse $reponse */
            $reponse = $object->reponse;
            $erreur = $object->erreur;

            $repStr = '<b>Aucune</b>';
            if ($reponse->getValeur() != "") {

                $data_value = json_decode($question->getListeValeur());
                $tmp = explode('|', $reponse->getValeur());

                if (in_array($question->getType(), array(
                    AppController::CHECKBOXTYPE,
                    AppController::CHOICETYPE,
                    AppController::RADIOTYPE
                ))) {

                    $repStr = '';
                    foreach ($data_value as $val) {

                        foreach ($tmp as $tmpVal) {
                            if ($tmpVal == $val->value) {
                                $repStr .= "<b>[" . $val->value . "] " . $val->libelle . '</b>, ';
                            }
                        }
                    }
                    $repStr = substr($repStr, 0, - 2);
                } else {
                    $repStr = '<b>' . $reponse->getValeur() . '</b>';
                }
            }

            $errStr = '';
            $icon = '<span class="text-success"><span class="oi oi-check"></span></span>';
            if ($erreur->is) {
                $errStr .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="oi oi-warning"></span> <span class="text-danger"><b>' . $erreur->regle . '</b> : ' . $erreur->libelle . '</span>';
                $icon = '<span class="text-danger"><span class="oi oi-x"></span></span>';
                $is_error = true;
            }

            $html .= '<div>';
            $html .= $icon . ' Question <b>#' . $question->getId() . '</b> ' . $question->getLibelle() . '<br />';

            $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="oi oi-arrow-right"></span> Le ' . $reponse->getDate()->format('d-m-Y à H:i:s') . ' - Reponse : ' . $repStr . '<br />' . $errStr;

            $html .= '</div><br />';
        }

        if ($is_error) {
            $border = 'border border-danger';
        } else {
            $border = 'border border-success';
        }

        $html = '<div class="card "><div class="card-body ' . $border . '">' . $html . '</div></div>';
        return $html;
    }

    /**
     *
     * @param Paginator $liste_membres
     * @param Questionnaire $questionnaire
     * @return string
     */
    public function displayMembreWithReponse(Paginator $liste_membres, Questionnaire $questionnaire)
    {
        $html = '<div id="listing_membres">';
        $i = 0;
        $aria_expanded = true;
        $show = 'show';

        if ($liste_membres->count() == 0) {
            return '<p class="text-info text-center">Il n\'y a aucun membre correspondant à votre recherche.</p>';
        }

        /* @var \App\Entity\Membre $membre */
        foreach ($liste_membres as $membre) {
            if ($i > 0) {
                $aria_expanded = false;
                $show = '';
            }

            $date_reponse = '';
            foreach ($questionnaire->getQuestions() as $question) {
                foreach ($question->getReponses() as $reponse) {
                    if ($reponse->getMembre()->getId() == $membre->getId()) {
                        $date_reponse = $reponse->getDate()->format('\L\e d-m-Y à H:i:s');
                        break;
                    }
                }
            }

            $html .= '<div class="card">
                <div class="card-header" id="card-' . $membre->getId() . '" data-toggle="collapse" data-target="#collapse-' . $membre->getId() . '" aria-expanded="' . $aria_expanded . '" aria-controls="collapse-' . $membre->getId() . '">
                    <div class="float-left">
                        #' . $membre->getId() . ' ' . $membre->getPrenom() . ' ' . $membre->getNom() . ' (' . $membre->getUsername() . ')
                    </div>
                    <div class="float-right">' . $date_reponse . '</div>
                </div>
                    
                <div id="collapse-' . $membre->getId() . '" class="collapse ' . $show . '" aria-labelledby="card-' . $membre->getId() . '" data-parent="#listing_membres">
                    <div class="card-body">';
            foreach ($questionnaire->getQuestions() as $question) {
                foreach ($question->getReponses() as $reponse) {
                    if ($reponse->getMembre()->getId() == $membre->getId()) {

                        if ($reponse->getValeur() != "") {

                            $data_value = json_decode($question->getListeValeur());
                            $tmp = explode('|', $reponse->getValeur());

                            if (in_array($question->getType(), array(
                                AppController::CHECKBOXTYPE,
                                AppController::CHOICETYPE,
                                AppController::RADIOTYPE
                            ))) {

                                $repStr = '';
                                foreach ($data_value as $val) {

                                    foreach ($tmp as $tmpVal) {
                                        if ($tmpVal == $val->value) {
                                            $repStr .= "<b>" . $val->libelle . '</b>, ';
                                        }
                                    }
                                }
                                $repStr = substr($repStr, 0, - 2);
                            } else {

                                $repStr = '<b>' . $reponse->getValeur() . '</b>';
                            }
                        } else {
                            $repStr = '<i>Pas de réponse saisie</i>';
                        }

                        $html .= '<div class="float-left">' . $question->getLibelle() . ' <br />
                            &nbsp;&nbsp;<span class="oi oi-arrow-thick-right"></span> ' . $repStr . '</div>';
                    }
                }
            }

            $html .= '</div>
                </div>
            </div>';

            $i ++;
        }

        $html .= '</div>';
        return $html;
    }
}
