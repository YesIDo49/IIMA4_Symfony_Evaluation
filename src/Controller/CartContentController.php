<?php

namespace App\Controller;

use App\Entity\CartContent;
use App\Form\CartContentType;
use App\Repository\CartContentRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/cart/content')]
class CartContentController extends AbstractController
{
    #[Route('/', name: 'app_cart_content_index', methods: ['GET'])]
    public function index(CartContentRepository $cartContentRepository): Response
    {
        return $this->render('cart_content/index.html.twig', [
            'cart_contents' => $cartContentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_cart_content_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ProductController $product): Response
    {
        $cartContent = new CartContent();
        $cartContent->setCart($this->getUser());
        $cartContent->setQuantity(1);
//        $cartContent->setProduct($product->id);

        $entityManager->persist($cartContent);
        $entityManager->flush();


        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[IsGranted('ROLE_USER')]
    #[Route('/add-to-cart/{productId}', name: 'app_add_to_cart', methods: ['GET'])]
    public function addToCart(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository, int $productId): Response
    {
        $product = $productRepository->find($productId);
        $user = $this->getUser();

        // Supposez que vous voulez utiliser le dernier panier de l'utilisateur.
        $carts = $user->getCarts();
        $cart = $carts->last();

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $existingCartItem = $cart->getCartContents()->filter(function (CartContent $cartContent) use ($product) {
            return $cartContent->getProduct() === $product;
        })->first();

        if ($existingCartItem) {
            // If the product exists, increment the quantity
            $existingCartItem->setQuantity($existingCartItem->getQuantity() + 1);
        } else {
            // If the product doesn't exist, create a new CartContent entry
            $cartContent = new CartContent();
            $cartContent->setCart($cart);
            $cartContent->setQuantity(1);
            $cartContent->setProduct($product);
            $cartContent->setDate(new \DateTime("now", new \DateTimeZone("Europe/Paris")));

            $entityManager->persist($cartContent);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_product_index');
    }

    #[Route('/{id}', name: 'app_cart_content_show', methods: ['GET'])]
    public function show(CartContent $cartContent): Response
    {
        return $this->render('cart_content/show.html.twig', [
            'cart_content' => $cartContent,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cart_content_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CartContent $cartContent, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CartContentType::class, $cartContent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cart_content_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cart_content/edit.html.twig', [
            'cart_content' => $cartContent,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cart_content_delete', methods: ['POST'])]
    public function delete(Request $request, CartContent $cartContent, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cartContent->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($cartContent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cart_content_index', [], Response::HTTP_SEE_OTHER);
    }
}
