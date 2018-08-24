<?php
namespace App\Form;

use App\Entity\Temoignage;
use App\Entity\Evenement;
use App\Entity\Produit;
use App\Controller\AppController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use App\Entity\Famille;

class TemoignageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titre')
            ->add('corps', TextareaType::class);

        if ($options['avec_event']) {
            $event_opt = array(
                'class' => Evenement::class,
                'disabled' => $options['disabled_event'],
                'choice_label' => 'nom',
                'required' => $options['required_event']
            );
            
            if (! is_null($options['query_event'])) {
                $event_opt['query_builder'] = $options['query_event'];
            }
            
            $builder->add('famille', EntityType::class, array('class' => Famille::class, 'choice_label' => 'nom'))
                ->add('evenement', EntityType::class, $event_opt);
        }

        if ($options['avec_prod']) {
            $prod_opt = array(
                'class' => Produit::class,
                'disabled' => $options['disabled_prod'],
                'choice_label' => 'titre',
                'required' => $options['required_prod']
            );
            
            if (! is_null($options['query_prod'])) {
                $prod_opt['query_builder'] = $options['query_prod'];
            }
            
            $builder->add('produit', EntityType::class, $prod_opt);
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
            'data_class' => Temoignage::class,
            'label_submit' => 'Valider',
            'famille_parente' => AppController::FAMILLE_PARENTE,
            'disabled_event' => false,
            'disabled_prod' => false,
            'avec_event' => true,
            'avec_prod' => true,
            'query_prod' => null,
            'query_event' => null,
            'required_prod' =>  false,
            'required_event' =>  false
        ]);
    }
}
