<?php
namespace App\Form;

use App\Entity\FamilleAdresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class FamilleAdresseType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('numero_voie', IntegerType::class, array(
            'label' => 'Numéro de voie'
        ))
            ->add('voie')
            ->add('ville')
            ->add('code_postal');

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
            'data_class' => FamilleAdresse::class,
            'label_submit' => 'Valider',
            'avec_bouton' => true
        ]);
    }
}
