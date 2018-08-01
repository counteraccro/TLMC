<?php
namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Controller\AppController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\DataMapper\CheckboxListMapper;

class QuestionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', ChoiceType::class, array(
            'choices' => array_flip(AppController::QUESTION_TYPE),
            'label' => 'Type de question',
            'required' => false,
            'attr' => array(
                'class' => 'preview'
            )
        ))
            ->add('libelle', TextareaType::class, array(
            'label' => 'Libellé du champ',
            'required' => false,
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
            'label' => 'Valeur par défaut',
            'required' => false,
            'attr' => array(
                'class' => 'preview-hidden'
            )
        ))
            ->add('message_erreur', TextType::class, array(
            'label' => 'Message d\'erreur',
            'empty_data' => 'Ce champ est incorrect',
            'required' => false,
            'attr' => array(
                'class' => 'preview'
            )
        ))
            ->add('ordre', NumberType::class, array(
            'required' => false
        ))
            ->add('regles', ChoiceType::class, array(
            'choices' => AppController::QUESTION_REGLES_REGEX,
            'label' => 'Règles',
            'required' => false,
            'attr' => array(
                'class' => 'preview'
            )
        ))
            ->add('obligatoire', CheckboxType::class, array(
            'required' => false,
            'label' => 'Obligatoire'
        ))
            ->add('disabled', CheckboxType::class, array(
            'required' => false,
            'label' => 'Actif'
        ))
            ->add('save', SubmitType::class, array(
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
            'ajax_button' => false
        ]);
    }
}
