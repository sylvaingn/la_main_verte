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
use Symfony\Component\HttpFoundation\Session\Session;
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
        $panier = $session->get('panier', []);
   
        if (!empty($panier[$id])) //si le panier n'est pas vide pour ce produit
        {
            $panier[$id]++; // on l'incrémente
        } else {
            $panier[$id] = 1;
        }

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

        $total = 0;
        foreach ($panierWithData as $item) {
            $totalItem = $item['stock']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }

        return $this->redirectToRoute("cart_index"); // affichage du panier à chaque nouvel article
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

       $entityManager->flush();

        //création des lignes détail :
        $id_ordered = $this->getUser(1);
        foreach ($futurCde as $key){
            $detail = new Detail();
            $detail->setOrdered($id_ordered);
            $detail->setQuantity($detail['quantity']);
            $detail->setStock($detail['stock']);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($detail);
        }
        $entityManager->flush();

        //suppression du panier
        $session->set('panier', []);

        return $this->redirectToRoute("app_index");
        $session->clear();
    }

}

