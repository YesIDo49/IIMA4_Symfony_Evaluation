<?php

namespace App\Controller;

use App\Entity\Cart;
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

        $carts = $user->getCarts();
        $cart = $carts->last();

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $cart->setState(false);
            $entityManager->persist($cart);
        }

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        if ($product->getStock() <= 0) {
            $this->addFlash('danger', 'This product is out of stock.');
            return $this->redirectToRoute('app_product_index');
        }

        $requestedQuantity = 1;
        if ($product->getStock() < $requestedQuantity) {
            $this->addFlash('danger', 'Quantity requested exceeds available stock for this product.');
            return $this->redirectToRoute('app_product_index');
        }

        $existingCartItem = $cart->getCartContents()->filter(function (CartContent $cartContent) use ($product) {
            return $cartContent->getProduct() === $product;
        })->first();

        if ($existingCartItem) {
            if ($existingCartItem->getQuantity() + $requestedQuantity > $product->getStock()) {
                $this->addFlash('danger', 'Quantity requested exceeds available stock for this product.');
                return $this->redirectToRoute('app_product_index');
            }

            $existingCartItem->setQuantity($existingCartItem->getQuantity() + $requestedQuantity);
        } else {
            $cartContent = new CartContent();
            $cartContent->setCart($cart);
            $cartContent->setQuantity($requestedQuantity);
            $cartContent->setProduct($product);
            $cartContent->setDate(new \DateTime("now", new \DateTimeZone("Europe/Paris")));

            $entityManager->persist($cartContent);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Product added to cart successfully.');

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

    #[Route('/{id}/add', name: 'app_cart_content_plus', methods: ['GET', 'POST'])]
    public function add(CartContent $cartContent, EntityManagerInterface $entityManager): Response
    {
      $product_stock = $cartContent->getProduct()->getStock();
      $quantity = $cartContent->getQuantity();

      if ($product_stock > $quantity) {
        $quantity += 1;
      } else {
        $this->addFlash('danger', 'Not enough stock');
      }

      $cartContent->setQuantity($quantity);
      $entityManager->persist($cartContent);
      $entityManager->flush();
      return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/minus', name: 'app_cart_content_minus', methods: ['GET', 'POST'])]
    public function minus(CartContent $cartContent, EntityManagerInterface $entityManager): Response
    {
      $quantity = $cartContent->getQuantity();

      if ($quantity === 1) {
        $this->addFlash('danger', 'Delete the product');
      } else {
        $quantity -= 1;
        $cartContent->setQuantity($quantity);
        $entityManager->persist($cartContent);
        $entityManager->flush();
      }
      return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);

    }

    #[Route('/{id}', name: 'app_cart_content_delete', methods: ['POST'])]
    public function delete(Request $request, CartContent $cartContent, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cartContent->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($cartContent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
    }
}
