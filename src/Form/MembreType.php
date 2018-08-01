<?php
namespace App\Form;

use App\Entity\Membre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Etablissement;
use App\Entity\Specialite;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MembreType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $first_options = array(
            'label' => 'Mot de passe',
            'empty_data' => 'password'
        );
        if ($options['edit']) {
            $first_options['help'] = 'Ne pas remplir les champs Mots de passe pour ne pas le modifier.';
        }

        $builder->add('nom')
            ->add('prenom', TextType::class, array(
            'label' => 'Prénom'
        ))
            ->add('username', TextType::class, array(
            'label' => 'Pseudo'
        ))
            ->add('password', RepeatedType::class, array(
            'first_options' => $first_options,
            'second_options' => array(
                'label' => 'Confirmation du mot de passe',
                'empty_data' => 'password'
            ),
            'type' => PasswordType::class,
            'required' => ($options['edit'] ? false : true),
            'invalid_message' => 'Les mots de passe doivent correspondre',
            'options' => array(
                'attr' => array(
                    'class' => 'password-field'
                )
            )
        ))
            ->add('numero_tel', TextType::class, array(
            'label' => 'Numéro de téléphone',
            'required' => false
        ))
            ->add('email', EmailType::class, array(
            'label' => 'Adresse email'
        ))
            ->add('fonction')
            ->add('decideur', CheckboxType::class, array(
            'label' => 'Décideur',
            'required' => false
        ))
            ->add('annuaire', CheckboxType::class, array(
            'label' => "Présent dans l'annuaire",
            'required' => false
        ))
            ->add('signature', TextareaType::class)
            ->add('etablissement', EntityType::class, array(
            'class' => Etablissement::class,
            'choice_label' => 'nom'
        ))
            ->add('specialite', EntityType::class, array(
            'class' => Specialite::class,
            'choice_label' => 'service',
            'label' => 'Spécialité',
            'required' => false
        ))
            ->add('roles', ChoiceType::class, array(
            'choices' => array(
                'Administratreur' => 'ROLE_ADMIN',
                'Bénéficiaire Direct' => 'ROLE_BENEFICIAIRE_DIRECT',
                'Bénéficiaire' => 'ROLE_BENEFICIAIRE',
                'Bénévole' => 'ROLE_BENEVOLE'
            ),
            'label' => 'Droits',
            'multiple' => true
        ))
            ->add('save', SubmitType::class, array(
            'label' => $options['label_submit'],
            'attr' => array(
                'class' => 'btn btn-primary'
            )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Membre::class,
            'label_submit' => 'Valider',
            'edit' => false
        ]);
    }
}
