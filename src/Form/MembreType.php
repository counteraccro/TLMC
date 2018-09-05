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
use App\Controller\AppController;
use App\Repository\EtablissementRepository;
use Symfony\Component\Form\Extension\Core\Type\FileType;

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
            ->add('annuaire', CheckboxType::class, array(
            'label' => "Présent dans l'annuaire",
            'required' => false
        ))
            ->add('signature', TextareaType::class)
            ->add('etablissement', EntityType::class, array(
            'class' => Etablissement::class,
            'disabled' => $options['disabled_etablissement'],
                'query_builder' => function (EtablissementRepository $er) {
                return $er->createQueryBuilder('e')
                ->andWhere('e.disabled = 0')
                ->orderBy('e.nom', 'ASC');
                },
            'choice_label' => 'nom'
        ))
            ->add('specialite', EntityType::class, array(
            'class' => Specialite::class,
            'choice_label' => 'service',
            'label' => 'Spécialité',
            'disabled' => $options['disabled_specialite'],
            'required' => false
        ));

        if ($options['admin']) {
            $builder->add('decideur', CheckboxType::class, array(
                'label' => 'Décideur',
                'required' => false
            ))->add('roles', ChoiceType::class, array(
                'choices' => array_flip($options['roles']),
                'label' => 'Droits',
                'multiple' => true
            ));
        }
        
        if (! $options['ajax']) {
            $builder->add('avatar', FileType::class, array(
                'label' => 'Avatar',
                'data_class' => null,
                'required' => false,
                'help' => ($options['edit'] ? 'Ne pas remplir si vous souhaitez conserver le même avatar' : ''),
                'attr' => array(
                    'placeholder' => 'Choisir un avatar'
                )
            ));
        }

        $builder->add('save', SubmitType::class, array(
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
            'edit' => false,
            'roles' => AppController::DROITS,
            'disabled_etablissement' => false,
            'disabled_specialite' => false,
            'admin' => true,
            'ajax' => false
        ]);
    }
}
