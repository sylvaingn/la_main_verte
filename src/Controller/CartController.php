<?php

namespace App\Controller;

use App\Entity\Detail;
use App\Entity\Ordered;
use App\Repository\ProductRepository;
use App\Repository\StockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{

    /**
     * @Route("/panier", name="cart_index")
     */
   public function index(SessionInterface $session, ProductRepository $productRepository,  StockRepository $stockRepository){

    $panier = $session->get('panier', []); 

    $panierWithData = [];
    foreach($panier as $id => $quantity)
    {
        $stock = $stockRepository->find($id);
        $produit = $productRepository->findOneBy(['id' =>  $stock->getProduct()]);
        $panierWithData[] = [
            'stock'   => $stock,
            'quantity'  =>  $quantity,
            'product'   =>  $produit
        ];
    }
    $total=0;
    foreach($panierWithData as $item){
        $totalItem = $item['stock']->getPrice() * $item['quantity'];
        $total += $totalItem;
    }

    return $this->render('cart/index.html.twig', [
        'items' =>  $panierWithData,
        'total' =>  $total
    ]);

    }

    /**
     * @Route("/panier/add/{id}", name="cart_add")
     */
    public function add($id, SessionInterface $session, ProductRepository $productRepository, StockRepository $stockRepository)
    {
        // on va passer en paramètre l'id du produit à ajouter au panier ($id)
        // on demande a session de nous retourner l'objet 'panier', qui par défaut sera un tableau vide
        $panier = $session->get('panier', []);
   
        // on va stocker dans notre panier des id auquels on va affecter les qtés
        if (!empty($panier[$id])) //si le panier n'est pas vide pour ce produit
        {
            $panier[$id]++; // on l'incrémente
        } else {
            $panier[$id] = 1;
        }

        // on sauvegarde le panier dans la session au fur et à mesure des ajouts
        // donc au demande a 'session' de remplacer notre panier précédent par le nouveau
        $session->set('panier', $panier);
        $panierWithData = [];
        foreach ($panier as $id => $quantity) {
            $stock = $stockRepository->find($id);
            $produit = $productRepository->findOneBy(['id' =>  $stock->getProduct()]);
            $panierWithData[] = [
                'stock'   => $stock,
                'quantity'  =>  $quantity,
                'product'   =>  $produit
            ];
        }

        //on calcule le montant total du panier
        $total = 0;
        foreach ($panierWithData as $item) {
            $totalItem = $item['stock']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }
        //lignes pour test à supprimer après test
        $id_company = 1;
        $stocks = $stockRepository->findBy(['company' => $id_company]);
        return $this->render('stock/ordered.html.twig', [
            'stocks' => $stocks,
            'total' => $total,
            'items' =>  $panierWithData
            ]);

        // ligne suivante à remettre après test
        // return $this->redirectToRoute("cart_index"); // affichage du panier à chaque nouvel article
    }

    /**
     * @Route("/panier/remove/{id}", name="cart_remove")
     */
    public function remove($id, SessionInterface $session)
    {
        // on  recupère le panier par la session
        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) // si le panier contient bien mon id
        {
            unset($panier[$id]); // on  vide l'élément
        }

        //on remet le panier dans la session
        $session->set('panier', $panier);
        return $this->redirectToRoute("cart_index");
    }

    // /**
    //  * @Route("/panier/testAjax", name="cart_ajax")
    // */
    // public function testAjaxPanier(Request $request, SessionInterface $session)
    // {
    //     // $contenu = $session->get('panier', []);
    //     // dd($contenu);

    //     // if($request->isXmlHttpRequest())
    //     // {
    //         $contenu = $session->get('panier', []);
    //         if ( count($contenu) == 0)
    //         {
    //             return new Response("panier vide");
    //         }else{
    //             return new JsonResponse(array('data' => json_encode($contenu)));
    //         }

    //     // }else{
    //         // return new Response("pas de requete");
    //     // }
    // }

    /**
     * @Route("createCommande", name="createCommande")
     */
    public function createCommande(SessionInterface $session, StockRepository $stockRepository)
    {
        $panier = $session->get('panier', []);
        $futurCde = [];

        foreach ($panier as $id => $quantity) {
            $stock = $stockRepository->find($id);
            $futurCde[] = [
                'stock'   => $stock,
                'quantity'  =>  $quantity,
            ];
        }

        //on calcule le montant total du panier
        $total = 0;
        foreach ($futurCde as $item) {
            $totalItem = $item['stock']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }

        // $id_user = $this->getUser(1); // pour test, à modifier après auth user
        // $id_company = $this->getUser(1); // pour test, résultat de la page Show de producteur
        // $id_drive = $this->getUser(1); // pour test, à faire choisr dans la liste des drive de company

        //création commande
        $entityManager = $this->getDoctrine()->getManager();

        $ordered = new Ordered();
        $ordered->setUser(1); 
        $ordered->setValidated(false);
        $ordered->setTotalPrice($total);
        $ordered->setCompany(1);
        $ordered->setDrive(1);

        $entityManager->persist($ordered);

        // dd($ordered);
        // $entityManager->flush();

        //création des lignes détail :
        $id_ordered = $this->getUser(1);
        foreach ($futurCde as $key){
            dd($key);
            $detail = new Detail();
            $detail->setOrdered($id_ordered);
            $detail->setQuantity($detail['quantity']);
            $detail->setStock($detail['stock']);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($detail);
        }
        // $entityManager->flush();

        //suppression du panier
        $session->set('panier', []);

        return $this->redirectToRoute("app_index");

    }

}

