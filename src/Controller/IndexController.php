<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;


class IndexController extends AppController
{

    /**
     *
     * @Route("/", name="index")
     */
    public function index()
    {

        // $repository = $this->getDoctrine()->getRepository(Patient::class);

        // $patient = new Patient();

        // look for a single Product by its primary key (usually "id")
        // $patients = $repository->findAll();

        // echo $patients[0]->getId();
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController'
        ]);
    }
}