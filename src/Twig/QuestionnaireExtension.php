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

                    ;
                    break;
                case 'TextareaType':
                    ;
                    break;
                case 'CheckboxType':
                    ;
                    break;
                case 'RadioType':
                    $html .= $this->RadioType($question);
                    ;
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
                    <label for="q-' . $question->getId() . '">' . $question->getLibelle() . '</label>
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

        $html .= '</div>';

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
                    <label for="q-' . $question->getId() . '">' . $question->getLibelle() . '</label>
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
        
        $html .= '</div>';
        
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
