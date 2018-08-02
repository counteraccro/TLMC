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
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController'
        ]);
    }
}