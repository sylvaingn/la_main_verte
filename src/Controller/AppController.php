<?php

namespace App\Controller;

use App\Repository\StockRepository;
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
     * @Route ("/a-propos", name="app_propos")
     *
     */

    public function info()
    {
        return $this->render('app/a-propos.html.twig');
    }

    /**********
     * PARTIE POUR TEST à SUPPRIMER
     */

    // /**
    //  * @Route("/testpanier", name="/testpanier")
    //  * test affichage panier - à supprimer après tests
    //  */
    // public function testPanier()
    //     {
    //         $panierWithData = [];
    //         $total = 0;

    //         return $this->render('cart/index.html.twig', [
    //         'items' =>  $panierWithData,
    //         'total' =>  $total
    //         ]);
    //     }

    /**
     * @Route("/testcommand", name="/testcommand")
     */
    public function testCommand(StockRepository $stockRepository)
    {
        $id_company = 1;
        $stocks = $stockRepository->findBy(['company' => $id_company]);
        // dd($stocks);

        return $this->render('stock/ordered.html.twig', ['stocks' => $stocks]);

    }



}
