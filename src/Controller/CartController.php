<?php

namespace App\Controller;

use App\Repository\StockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class CartController extends AbstractController
{

    /**
     * @Route("/panier", name="cart_index")
     */
   public function index(SessionInterface $session, StockRepository $stockRepository){
    // on utilise la session et on lui demande de recupérer le panier :
    $panier = $session->get('panier', []); 
    //  par défaut on prend un panier vide

    //à partir de ce tableau ($panier) qui ne contient que id et qté, on va créer un nouveau tableau
    //qu'on va enrichir avec les data (nom, description, image, ...)
    $panierWithData = [];

    foreach($panier as $id => $quantity)
    {
        $panierWithData[] = [
            'stock'   => $stockRepository->find($id),
            'quantity'  =>  $quantity
        ];
    }

    //on calcule le montant total du panier
    $total=0;
    foreach($panierWithData as $item){
        $totalItem = $item['stock']->getPrice() * $item['quantity'];
        $total += $totalItem;
    }

    // dd($panierWithData);

    return $this->render('cart/index.html.twig', [
        'items' =>  $panierWithData,
        'total' =>  $total
    ]);

}

    /**
     * @Route("/panier/add/{id}", name="cart_add")
     */
    public function add($id, SessionInterface $session, StockRepository $stockRepository)
    {
        // on va passer en paramètre l'id du produit à ajouter au panier ($id)

        // il faut accéder à la session on va utiliser le service SessionInterface
        // on récupère un objet retourné par ce service cet objet sera notre panier
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

        //lignes pour test à supprimer après test
        $id_company = 1;
        $stocks = $stockRepository->findBy(['company' => $id_company]);
        return $this->render('stock/ordered.html.twig', ['stocks' => $stocks]);

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


}

