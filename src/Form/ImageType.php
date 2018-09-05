<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ImageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('image', FileType::class, array(
            'label' => 'Télécharger mon nouvel avatar :',
            'data_class' => null,
            'required' => false,
            'attr' => array(
                'placeholder' => 'Uploader un nouvel avatar'
            )))
            ->add('save', SubmitType::class, array(
            'label' => 'Sauvegarder',
            'attr' => array(
                'class' => 'btn btn-primary'
            )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(

        ));
    }
}
