<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    public function __construct(private readonly ProductRepository $productRepository)
    {
        
    }
    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function index(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        $cartWithData = [];

        foreach ($cart as $item) {
            if (isset($item['id'], $item['size'], $item['quantity'])) {
                $product = $this->productRepository->find($item['id']);
                if ($product) {
                    $cartWithData[] = [
                        'product' => $product,
                        'size' => $item['size'],
                        'quantity' => $item['quantity'],
                    ];
                }
            }
        }

        $total = array_sum(array_map(function($item) {
            return $item['product']->getPrice() * $item['quantity'];
        }, $cartWithData));

        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'total' => $total,
            'page_title' => 'Panier',
        ]);
    }

    #[Route('/cart/add/{id}/', name: 'app_cart_new', methods: ['POST'])]
    public function addToCart(int $id, Request $request, SessionInterface $session): Response
    {
        $size = $request->request->get('size');
        $cart = $session->get('cart', []);

        $found = false;
        foreach ($cart as &$item) {
            if (is_array($item) && isset($item['id']) && isset($item['size']) && $item['id'] === $id && $item['size'] === $size) {
                $item['quantity']++;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $cart[] = [
                'id' => $id,
                'size' => $size,
                'quantity' => 1,
            ];
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id}/{size}', name: 'app_cart_product_remove', methods: ['GET'])]
    public function removeToCart(int $id, string $size, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        foreach ($cart as $key => $item) {
            if (is_array($item) && isset($item['id']) && isset($item['size']) && $item['id'] === $id && $item['size'] === $size) {
                unset($cart[$key]);
                break;
            }
        }

        $session->set('cart', array_values($cart)); 

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/', name: 'app_cart_remove', methods: ['GET'])]
    public function remove(SessionInterface $session): Response
    {
        $session->set('cart', []);
        return $this->redirectToRoute('app_cart');
    }
    
}
