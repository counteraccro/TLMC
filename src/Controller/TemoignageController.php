<?php
namespace App\Controller;

use App\Entity\Temoignage;
use Symfony\Component\Routing\Annotation\Route;

class TemoignageController extends AppController
{

    /**
     *
     * @Route("/temoignage", name="temoignage")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Temoignage::class);

        // look for a single Product by its primary key (usually "id")
        $temoignages = $repository->findAll();

        return $this->render('temoignage/index.html.twig', [
            'controller_name' => 'TemoignageController',
            'temoignages' => $temoignages
        ]);
    }
}
