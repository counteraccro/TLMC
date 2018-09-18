<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\FamillePatientType;
use App\Entity\FamillePatient;

class FamillePatientController extends AbstractController
{
    /**
     * @Route("/famille_patient", name="famille_patient")
     */
    public function index()
    {
        $famille_patient = new FamillePatient();
        
        $form = $this->createForm(FamillePatientType::class, $famille_patient, array(
            'label_submit' => 'Ajouter'
        ));
        
        return $this->render('famille_patient/index.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
