<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Entity\Questionnaire;

class QuestionnaireActionExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('addQuestion', [
                $this,
                'addQuestionBtn'
            ]),
            new TwigFunction('edit', [
                $this,
                'editBtn'
            ]),
            new TwigFunction('publish', [
                $this,
                'publishBtn'
            ]),
            new TwigFunction('test', [
                $this,
                'testBtn'
            ]),
            new TwigFunction('duplicate', [
                $this,
                'duplicateBtn'
            ])
        ];
    }

    public function addQuestionBtn(Questionnaire $questionnaire, $url)
    {
        $disabled = '';
        if (new \DateTime('now') > $questionnaire->getDateFin()) {
            $disabled = 'disabled';
        }

        if ($questionnaire->getPublication()) {
            $disabled = 'disabled';
        }

        return '<a href="' . $url . '" id="btn-add-question" class="btn btn-primary btn-add-question ' . $disabled . '"> <span class="oi oi-plus"></span> Nouvelle question</a>';
    }

    public function editBtn(Questionnaire $questionnaire, $url)
    {
        $disabled = '';
        $bloc_begin = $bloc_end = '';

        // Cas ou le questionnaire est publié et qu'au moins 1 réponse à été faite sur 1 question
        $question = $questionnaire->getQuestions()->first();
        if (! empty($question)) {
            if ($question->getReponses()->count() > 0 && $questionnaire->getPublication()) {

                $bloc_begin = '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Edition vérouillée car publié et possède déjà au moins 1 réponse">';
                $disabled = 'disabled';
                $bloc_end = '</span>';
            }
        }
        return $bloc_begin . '<a href="' . $url . '" id="btn-edit-questionnaire" class="btn btn-dark ' . $disabled . '"><span class="oi oi-pencil"></span> Editer</a>' . $bloc_end;
    }

    public function publishBtn(Questionnaire $questionnaire, $url)
    {
        $disabled = '';
        $bloc_begin = $bloc_end = '';
        if (new \DateTime('now') > $questionnaire->getDateFin()) {
            $disabled = 'disabled';
        }

        // Cas ou le questionnaire est publié et qu'au moins 1 réponse à été faite sur 1 question
        $question = $questionnaire->getQuestions()->first();
        if (! empty($question)) {
            if ($question->getReponses()->count() > 0 && $questionnaire->getPublication()) {

                $bloc_begin = '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Dépublication déconseillée car publié et possède déjà au moins 1 réponse">';
                $bloc_end = '</span>';
                return $bloc_begin . '<a href="' . $url . '" id="btn-publish-questionnaire" class="btn btn-success" data-publish="1"><span class="oi oi-action-undo"></span> Dépublier</a>' . $bloc_end;
            } else {

                $is_ok = false;
                foreach ($questionnaire->getQuestions() as $question) {
                    if (! $question->getDisabled()) {
                        $is_ok = true;
                    }
                }

                // Si toute les questions sont disabled alors on bloque le choix de publication
                if ($is_ok === false) {
                    $disabled = 'disabled';
                }

                // Cas publication possible
                if ($questionnaire->getPublication()) {
                    return '<a href="' . $url . '" id="btn-publish-questionnaire" class="btn btn-success ' . $disabled . '" data-publish="1"><span class="oi oi-action-undo"></span> Dépublier</a>';
                } // Cas dépublication possible
                else {
                    return '<a href="' . $url . '" id="btn-publish-questionnaire" class="btn btn-success ' . $disabled . '" data-publish="0"><span class="oi oi-share"></span> Publier</a>';
                }
            }
        } else {

            if ($disabled != 'disabled') {
                $bloc_begin = '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Le questionnaire doit avoir au moins 1 question pour être publié">';
                $bloc_end = '</span>';
            }
            return $bloc_begin . '<a href="' . $url . '" id="btn-publish-questionnaire" class="btn btn-success disabled"><span class="oi oi-share"></span> Publier</a>' . $bloc_end;
        }
    }

    public function testBtn(Questionnaire $questionnaire, $url)
    {
        $disabled = '';
        $disabled = '';
        $bloc_begin = $bloc_end = '';

        // Cas ou le questionnaire est publié et qu'au moins 1 réponse à été faite sur 1 question
        $is_ok = false;
        foreach ($questionnaire->getQuestions() as $question) {
            if (! $question->getDisabled()) {
                $is_ok = true;
            }
        }
        if ($is_ok === false) {
            $bloc_begin = '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Pour tester le questionnaire, celui ci doit posséder au moins 1 question">';
            $disabled = 'disabled';
            $bloc_end = '</span>';
        }

        return $bloc_begin . '<a href="' . $url . '" id="btn-demo-questionnaire" class="btn btn-info ' . $disabled . '"><span class="oi oi-monitor"></span> Tester</a>' . $bloc_end;
    }

    public function duplicateBtn(Questionnaire $questionnaire, $url)
    {
        return '<a href="' . $url . '" id="btn-duplicate-questionnaire" class="btn btn-secondary"><span class="oi oi-fork"></span> Dupliquer</a>';
    }
}
