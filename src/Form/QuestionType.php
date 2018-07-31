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
            'empty_data' => 'exemple-cle1:exemple-valeur1,
exemple-cle2:exemple-valeur2,',
            'required' => false,
            'attr' => array(
                'class' => 'preview'
            )
        ))
            ->add('valeur_defaut', ChoiceType::class, array(
            'label' => 'Valeur par défaut',
            'required' => false,
            'attr' => array(
                'class' => 'preview'
            )
        ))
            ->add('message_erreur', TextType::class, array(
            'label' => 'Message d\'erreur',
            'required' => false,
            'attr' => array(
                'class' => 'preview'
            )
        ))
            ->add('ordre', NumberType::class, array(
            'required' => false
        ))
            ->add('regles', TextareaType::class, array(
            'required' => false
        ))
            ->add('obligatoire', TextType::class, array(
            'required' => false
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
