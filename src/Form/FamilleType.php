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

class FamilleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $lien_parente = array();
        foreach ($options['famille_parente'] as $key => $val) {
            $lien_parente[$val] = $key;
        }

        $builder->add('nom')
            ->add('prenom', TextType::class, array(
            'label' => 'Prénom'
        ))
            ->add('lien_famille', ChoiceType::class, array(
            'label' => 'Lien de famille avec le patient',
            'choices' => $lien_parente
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
        ))
            ->add('save', SubmitType::class, array(
            'label' => $options['label_submit']
        ));

        // ->add('patient');
        // ->add('famille_adresse', EntityType::class, array('class' => FamilleAdresse::class, 'choice_label' => 'voie'))
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Famille::class,
            'famille_parente' => AppController::FAMILLE_PARENTE,
            'label_submit' => 'Valider'
        ]);
    }
}
