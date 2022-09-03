<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(RequestStack $rs, ProductRepository $repo): Response
    {
        $session = $rs->getSession();
        $cart = $session->get('cart', []);
        // je vais créer un nouveau tableau qui contiendra des objets Product et les quantités de chaque objet
        $cartWithData = [];
        // $cartWithData[] est un tableau multidimensionnel : pour chaque id qui se trouve dans le panier, nous allons créer un nouveau tableau dans $cartWithData[] qui contiendra 2 cases : product et quantity
        foreach($cart as $id => $quantity)
        {
            $cartWithData[] = [
                'product' => $repo->find($id), 'quantity' => $quantity
            ];
            // cette syntaxe signifie : je crée une nouvelle case dans $cartWithData
        }
        dd($cartWithData);
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
        ]);
        $total = 0; // j'initialise mon total
        // pour chaque produit dans mon panier, je récupère le total par produit puis je l'ajoute au total final

        foreach ($cartWithData as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }
        // return $this->render('cart/index.html.twig', [
        //     'items' => $cartWithData
        //     'total' => $total // j'envoie le total au template
        // ]);
    }
    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add($id, RequestStack $rs)
    {
        // nous allons récupérer ou créer une session grâce à la classe RequestStack
        $session = $rs->getSession();
        $cart = $session->get('cart', []);
        // je récupère l'attribut de session 'cart' s'il existe un tableau vide
        // si le produit existe déjà dans mon panier, j'incrémente sa quantité
        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
            // dans mon tableau $cart, à la case $id (qui correspond à l'id d'un produit), je donne la valeur 1
        }
        $session->set('cart', $cart);
        // je sauvegarde l'état de mon panier en sesion à l'attribut de session 'cart'
        dd($session->get('cart'));
    }
    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove($id, RequestStack $rs)
    {
        $session = $rs->getSession();
        $cart = $session->get('cart', []);
        // si l'id existe dans mon panier, je le supprime du tableau via unset()
        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }
        $session->set('cart', $cart);
        return $this->redirectToRoute('app_cart');
    }
}

