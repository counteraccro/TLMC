<?php
namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MessageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titre', TextType::class, array(
            'label' => 'Titre de votre message'
        ))
            ->add('corps', TextareaType::class, array(
            'label' => 'Contenu de votre message',
            'required' => false
        ))
            //->add('brouillon')
            //->
        // ->add('destinataire')
        // ->add('expediteur')
        // ->add('message');
        ->add('submit', SubmitType::class, array(
            'label' => 'Envoyer',
            'attr' => array(
                'class' => 'btn btn-primary'
            )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class
        ]);
    }
}
