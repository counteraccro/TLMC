<?php
namespace App\Form;

use App\Controller\AppController;
use App\Entity\Question;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', ChoiceType::class, array(
            'choices' => array_flip(AppController::QUESTION_TYPE),
            'label' => 'Type de question',
            'required' => true,
            'attr' => array(
                'class' => 'preview'
            )
        ))
            ->add('libelle', TextareaType::class, array(
            'label' => 'Libellé du champ',
            'required' => true,
            'attr' => array(
                'class' => 'preview'
            )
        ))
            ->add('libelle_bottom', TextareaType::class, array(
            'label' => 'Libellé informatif',
            'required' => false,
            'attr' => array(
                'class' => 'preview'
            )
        ))
            ->add('libelle_top', TextareaType::class, array(
            'label' => 'texte optionnel',
            'required' => false,
            'attr' => array(
                'class' => 'preview'
            )
        ))
            ->add('liste_valeur', TextareaType::class, array(
            'label' => 'Valeurs présentes dans la question',
            'required' => false,
            'attr' => array(
                'class' => 'preview'
            )
        ))
            ->add('valeur_defaut', TextType::class, array(
            'label' => 'Texte informatif pré-indiqué dans la question',
            'required' => false,
            'attr' => array(
                'class' => 'preview'
            )
        ))
            ->add('message_erreur', TextType::class, array(
            'label' => 'Message d\'erreur',
            'empty_data' => 'Ce champ est incorrect',
            'required' => false,
            'attr' => array(
                'class' => 'preview'
            )
        ));
        if ($options['statut'] == 'edit') {
            $builder->add('ordre', ChoiceType::class, array(
                'choices' => array_flip($options['questions']),
                'label' => 'Position dans le questionnaire',
                'required' => true
            ));
        }
        $builder->add('regles', ChoiceType::class, array(
            'choices' => array_flip(AppController::QUESTION_REGLES_REGEX),
            'label' => 'Règles',
            'empty_data' => '.',
            'required' => true,
            'attr' => array(
                'class' => 'preview'
            )
        ))->add('obligatoire', CheckboxType::class, array(
            'required' => false,
            'label' => 'Rendre cette question obligatoire'
        ));

        if ($options['statut'] == 'edit') {
            $builder->add('disabled', CheckboxType::class, array(
                'required' => false,
                'label' => 'Désactiver la question'
            ));
        }
        $builder->add('save', SubmitType::class, array(
            'label' => 'Valider',
            'attr' => array(
                'class' => 'btn btn-primary'
            )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
            'ajax_button' => false,
            'questions' => Collection::class,
            'statut' => false
        ]);
    }
}
