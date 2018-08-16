<?php
namespace App\Form;

use App\Entity\Questionnaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\DomCrawler\Field\TextareaFormField;

class QuestionnaireType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titre')
        
        ->add('description', TextareaType::class, array(
            'label' => 'Description du questionnaire'
        ));
        /*
         * ->add('date_creation', DateTimeType::class, array(
         * 'label' => 'Date de création',
         * 'widget' => 'choice',
         * 'years' => range(date('Y') - 100, date('Y')),
         * 'date_format' => 'dd MM yyyy'
         * ))
         */
        if ($options['isAdd']) {
            $builder->add('date_fin', DateType::class, array(
                'label' => 'Date de fin',
                'widget' => 'choice',
                'years' => range(date('Y'), date('Y') + 5),
                'format' => 'dd MM yyyy',
                'data' => new \DateTime('now + 1 month')
            ));
        } else {
            $builder->add('date_fin', DateType::class, array(
                'label' => 'Date de fin',
                'widget' => 'choice',
                'years' => range(date('Y') - 15, date('Y') + 10),
                'format' => 'dd MM yyyy'
            ));
        }

        $builder->add('jour_relance', NumberType::class, array(
            'label' => 'Jour de relance'
        ))->add('save', SubmitType::class, array(
            'label' => 'Valider',
            'attr' => array(
                'class' => 'btn btn-primary'
            )
        ));
        if ($options['isAdd'] == true) {
            $builder->add('slug', HiddenType::class, array(
                'attr' => [
                    'id' => 'slug_hidden'
                ]
            ));
        } else {
            $builder->add('slug', TextType::class, array(
                'attr' => [
                    'id' => 'slug_hidden'
                ],
                'disabled' => true
            ));
        }

        // $builder->add('publication');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Questionnaire::class,
            'isAdd' => false
        ]);
    }
}
