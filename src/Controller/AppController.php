<?php

namespace App\Controller;

use App\Repository\CompanyRepository;
use App\Repository\StockRepository;
use Symfony\Component\Routing\Annotation\Route;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{

    /**
     * @Route ("/", name="app_index")
     *
     */

    public function index(CompanyRepository $companyRepository)
    {
        return $this->render('app/index.html.twig' ,[
            "companies" => $companyRepository->findTenBestCompanies()
        ] );
    }


    /**
     * @Route ("/a-propos", name="app_propos")
     *
     */
    public function info()
    {
        return $this->render('app/a-propos.html.twig');
    }

    /**
     * @Route ("/mentions", name="app_mentions")
     *
     */
    public function mentions()
    {
        return $this->render('app/mentions.html.twig');
    }

    /**
     * @Route ("/rgpd", name="app_rgpd")
     *
     */
    public function rgpd()
    {
        return $this->render('app/rgpd.html.twig');
    }

    /**
     * @Route ("/cgu", name="app_cgu")
     *
     */
    public function cgu()
    {
        return $this->render('app/cgu.html.twig');
    }

    /**********
     * PARTIE POUR TEST à SUPPRIMER
     */

     /**
     * @Route("/testcommand", name="/testcommand")
     */
    public function testCommand(StockRepository $stockRepository)
    {
        $id_company = 1;
        $stocks = $stockRepository->findBy(['company' => $id_company]);
        $panier = []; // on démarre avec un panier vide
        $total = 0; //dont le total est vide aussi

        return $this->render('stock/ordered.html.twig', [
            'stocks' => $stocks,
            'items' => $panier,
            'total' =>  $total
            ]);

    }



}
