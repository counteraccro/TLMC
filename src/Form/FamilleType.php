<?php
namespace App\Form;

use App\Entity\Famille;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Controller\AppController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Patient;

class FamilleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['avec_patient']) {
            $builder->add('patient', EntityType::class, array(
                'class' => Patient::class,
                'disabled' => $options['disabled_patient'],
                'choice_label' => function (Patient $patient) {
                    return $patient->getPrenom() . ' ' . $patient->getNom() . ' (' . $patient->getDateNaissance()
                        ->format('d/m/Y') . ')';
                }
            ));
        }

        $builder->add('nom')
            ->add('prenom', TextType::class, array(
            'label' => 'Prénom'
        ))
            ->add('lien_famille', ChoiceType::class, array(
            'label' => 'Lien de famille avec le patient',
            'choices' => array_flip($options['famille_parente'])
        ))
            ->add('email', EmailType::class, array(
            'label' => 'Adresse email'
        ))
            ->add('numero_tel', TextType::class, array(
            'label' => 'Numéro de téléphone'
        ))
            ->add('pmr', CheckboxType::class, array(
            'label' => 'Personne à mobilité réduite',
            'required' => false
        ));

        $builder->add('famille_adresse', FamilleAdresseType::class, array(
            'label' => $options['label_adresse'],
            'avec_bouton' => false,
            'attr' => array(
                'class' => 'shadow-sm p-3 mb-5 bg-white rounded border'
            )
        ));

        if ($options['avec_bouton']) {
            $builder->add('save', SubmitType::class, array(
                'label' => $options['label_submit'],
                'attr' => array(
                    'class' => 'btn btn-primary'
                )
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Famille::class,
            'famille_parente' => AppController::FAMILLE_PARENTE,
            'label_submit' => 'Valider',
            'avec_bouton' => true,
            'avec_patient' => true,
            'disabled_patient' => false,
            'label_adresse' => 'Adresse'
        ]);
    }
}
