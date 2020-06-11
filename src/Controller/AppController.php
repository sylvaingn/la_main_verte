<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{

    /**
     * @Route ("/", name="app_index")
     *
     */

    public function index()
    {
        return $this->render('app/index.html.twig');
    }

    /**
     * @Route ("/contact", name="app_contact")
     *
     */


    /**
     * @Route ("/a-propos", name="app_propos")
     *
     */

    public function info()
    {
        return $this->render('app/a-propos.html.twig');
    }

    
}
