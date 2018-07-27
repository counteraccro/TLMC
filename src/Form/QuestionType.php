<?php
namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class QuestionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('libelle')
            ->add('libelle_top')
            ->add('libelle_bottom')
            ->add('type')
            ->add('valeur_defaut')
            ->add('liste_valeur')
            ->add('message_erreur')
            ->add('ordre')
            ->add('regles')
            ->add('obligatoire')
            // ->add('jour_relance', NumberType::class, array(
        // 'label' => 'Jour de relance'
        // ));
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
