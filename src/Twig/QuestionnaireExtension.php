<?php
/**
 * Traitement des booléens dans twig
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Entity\Questionnaire;
use App\Entity\Question;

class QuestionnaireExtension extends AbstractExtension
{

    /**
     * 
     * @var string
     */
    private $url = '';
    
    public function getFunctions(): array
    {
        return array(
            new TwigFunction('questionnaire', array(
                $this,
                'questionnaire'
            ))
        );
    }

    /**
     *
     * @param Questionnaire $questionnaire
     * @return string
     */
    public function questionnaire(Questionnaire $questionnaire, $url)
    {
        $this->url = $url;
        
        $html = '';
        
        $html .= $this->BeginForm();

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
                    ;
                    break;
            }
        }
        
        $html .= $this->endForm();
        
        return $html;
    }

    /**
     *
     * @param Question $question
     */
    private function ChoiceType(Question $question)
    {
        $html = '';
        $html .= '<div id="bloc-' . $question->getId() . '">';

        if (! empty($question->getLibelleTop())) {
            $html .= '<p>' . $question->getLibelleTop() . '</p>';
        }

        $html .= '<div class="form-group">
                    <label for="q-' . $question->getId() . '">' . $question->getLibelle() . " - " . "ID # " . $question->getId() . '</label>
                    <select class="form-control" id="q-' . $question->getId() . '" name="questionnaire[question][q-' . $question->getId() . ']">';
        
        $data_value = json_decode($question->getListeValeur());
        foreach($data_value as $val)
        {
            $selected = '';
            if($val->value == $question->getValeurDefaut())
            {
                $selected = ' selected';
            }
            
            $html .= '<option value="' . $val->value . '"' . $selected . '>' . $val->libelle . '</option>';
        }
        $html .= '</select>';

        if (! empty($question->getLibelleBottom())) {
            $html .= '<small id="help-q-' . $question->getId() . '" class="form-text text-muted">' . $question->getLibelleBottom() . '</small>';
        }

        $html .= '</div>';

        $html .= '</div><hr>';

        return $html;
    }
    
    /**
     *
     * @param Question $question
     */
    private function TextType(Question $question)
    {
         $html = '';
        $html .= '<div id="bloc-' . $question->getId() . '">';
        
        if (! empty($question->getLibelleTop())) {
            $html .= '<p>' . $question->getLibelleTop() . '</p>';
        }
        
        $html .= '<div class="form-group">
                    <label for="q-' . $question->getId() . '">' . $question->getLibelle() . " - " . "ID # " . $question->getId() . '</label>
                    <input class="form-control" id="q-' . $question->getId() . '" name="questionnaire[question][q-' . $question->getId() . ']">';
            
        if (! empty($question->getLibelleBottom())) {
            $html .= '<small id="help-q-' . $question->getId() . '" class="form-text text-muted">' . $question->getLibelleBottom() . '</small>';
        }
        
        $html .= '</div>';
        
        $html .= '</div><hr>';
        
        return $html;
    }
    
    /**
     *
     * @param Question $question
     */
    private function TextAreaType(Question $question)
    {
        $html = '';
        $html .= '<div id="bloc-' . $question->getId() . '">';
        
        if (! empty($question->getLibelleTop())) {
            $html .= '<p>' . $question->getLibelleTop() . '</p>';
        }
        
        $html .= '<div class="form-group">
                    <label for="q-' . $question->getId() . '">' . $question->getLibelle() . " - " . "ID # " . $question->getId() . '</label>
                    <textarea  class="form-control" id="q-' . $question->getId() . '" name="questionnaire[question][q-' . $question->getId() . ']">';
        $html .= '</textarea>';
        
        if (! empty($question->getLibelleBottom())) {
            $html .= '<small id="help-q-' . $question->getId() . '" class="form-text text-muted">' . $question->getLibelleBottom() . '</small>';
        }
     
        $html .= '</div>';
       
        $html .= '</div><hr>';
        
        return $html;
    }
    
    /**
     *
     * @param Question $question
     */
    private function CheckboxType(Question $question)
    {
        $html = '';
        $html .= '<div id="bloc-' . $question->getId() . '">';
        
        if (! empty($question->getLibelleTop())) {
            $html .= '<p>' . $question->getLibelleTop() . '</p>';
        }
        
        $html .= '<div class="form-group">
                    <label for="q-' . $question->getId() . '">' . $question->getLibelle() . " - " . "ID # " . $question->getId() . '</label>
                    <div class="form-check">';
                    
        $data_value = json_decode($question->getListeValeur());
        foreach($data_value as $val)
        {
            $checked = '';
            if($val->value == $question->getValeurDefaut())
            {
                $checked = ' checked';
            }
          
            $html .= '<input class="form-check-input" type="checkbox" value="' . $val->value . '"' . $checked . '" id="q-' . $question->getId() . '" name="questionnaire[question][q-' . $question->getId() . ']">';
//             $tmp = $question->getListeValeur();
//             $valeur_label = (json_decode($tmp));
            
//             foreach ($valeur_label as $key => $value)
//             {
//                 var_dump ('<strong>', $key, '</strong> => ', $value, '<br />');
//             }
            $html .= '<label class="form-check-label" for="q-' . $question->getId() . '">' . "récupérer les valeurs de l'objet (libelle)" . '</label><br>';
        }
        $html .= '</select>';
        
        if (! empty($question->getLibelleBottom())) {
            $html .= '<small id="help-q-' . $question->getId() . '" class="form-text text-muted">' . $question->getLibelleBottom() . '</small>';
        }
        
        $html .= '</div>';
        
        $html .= '</div>';
        
        $html .= '</div><hr>';
        
        return $html;
    }
    
    /**
     *
     * @param Question $question
     */
    private function RadioType(Question $question)
    {
        $html = '';
        $html .= '<div id="bloc-' . $question->getId() . '">';
        
        if (! empty($question->getLibelleTop())) {
            $html .= '<p>' . $question->getLibelleTop() . '</p>';
        }
        
        $html .= '<div class="form-group">
                    <label for="q-' . $question->getId() . '">' . $question->getLibelle() . " - " . "ID # " . $question->getId() . '</label>
                    <div class="form-check">';
        
        $data_value = json_decode($question->getListeValeur());
        foreach($data_value as $val)
        {
            $checked = '';
            if($val->value == $question->getValeurDefaut())
            {
                $checked = ' checked';
            }
            
            $html .='<input class="form-check-input" type="radio" value="' . $val->value . '"' . $checked . '" id="q-' . $question->getId() . '" name="questionnaire[question][q-' . $question->getId() . ']">';
            $html .= '<label class="form-check-label" for="q-' . $question->getId() . '">' .$val->value . '</label><br>';
        }
        
        if (! empty($question->getLibelleBottom())) {
            $html .= '<small id="help-q-' . $question->getId() . '" class="form-text text-muted">' . $question->getLibelleBottom() . '</small>';
        }
        
        $html .= '</div>';
        
        $html .= '</div>';
        
        $html .= '</div><hr>';
        
        return $html;
    }  
    
    /**
     * 
     */
    private function BeginForm()
    {
        $html = "";
        
        $html .='<form action="' . $this->url . '" method="post" class="">';
        
        return $html;
    }
    
    /**
     * 
     */
    private function endForm()
    {
        $html = "";
        
        $html .= '<button type="submit" name="validation" class="btn btn-primary mb-2">Valider</button>';
        $html .="</form>";
        
        return $html;
    }
}
