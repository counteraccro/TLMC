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
        $builder->add('image_1', FileType::class, array(
            'data_class' => null,
            'required' => false,
            'help' => $options['aide'],
            'attr' => array(
                'placeholder' => 'Choisir la première image'
            )))
                ->add('image_2', FileType::class, array(
                    'data_class' => null,
                    'required' => false,
                    'help' => $options['aide'],
                    'attr' => array(
                        'placeholder' => 'Choisir la deuxième image'
                    )))
                ->add('image_3', FileType::class, array(
                    'data_class' => null,
                    'required' => false,
                    'help' => $options['aide'],
                    'attr' => array(
                        'placeholder' => 'Choisir la troisième image'
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
            'aide' => 'Ne pas remplir si vous souhaitez conserver la même image'
        ));
    }
}
