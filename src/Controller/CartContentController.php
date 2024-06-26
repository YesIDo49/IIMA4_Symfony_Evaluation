<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartContent;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/cart/content')]
class CartContentController extends AbstractController
{

    #[Route('/new', name: 'app_cart_content_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager): Response
    {
        $cartContent = new CartContent();
        $cartContent->setCart($this->getUser());
        $cartContent->setQuantity(1);

        $entityManager->persist($cartContent);
        $entityManager->flush();


        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/add-to-cart/{productId}', name: 'app_add_to_cart', methods: ['GET'])]
    public function addToCart(EntityManagerInterface $entityManager, ProductRepository $productRepository, int $productId, TranslatorInterface $translator): Response
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
            $this->addFlash('danger', $translator->trans('alerts.product.out_of_stock'));
            return $this->redirectToRoute('app_product_index');
        }

        $requestedQuantity = 1;
        if ($product->getStock() < $requestedQuantity) {
            $this->addFlash('danger', $translator->trans('alerts.product.quantity'));
            return $this->redirectToRoute('app_product_index');
        }

        $existingCartItem = $cart->getCartContents()->filter(function (CartContent $cartContent) use ($product) {
            return $cartContent->getProduct() === $product;
        })->first();

        if ($existingCartItem) {
            if ($existingCartItem->getQuantity() + $requestedQuantity > $product->getStock()) {
                $this->addFlash('danger', $translator->trans('alerts.product.quantity'));
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

        $this->addFlash('success', $translator->trans('alerts.product.add_to_cart', ['%product%' => $product->getName()]));

        return $this->redirectToRoute('app_product_index');
    }

    #[Route('/{id}/add', name: 'app_cart_content_plus', methods: ['GET', 'POST'])]
    public function add(CartContent $cartContent, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
      $product_stock = $cartContent->getProduct()->getStock();
      $quantity = $cartContent->getQuantity();

      if ($product_stock > $quantity) {
        $quantity += 1;
      } else {
        $this->addFlash('danger', $translator->trans('alerts.product.quantity'));
      }

      $cartContent->setQuantity($quantity);
      $entityManager->persist($cartContent);
      $entityManager->flush();
      return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/minus', name: 'app_cart_content_minus', methods: ['GET', 'POST'])]
    public function minus(CartContent $cartContent, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
      $quantity = $cartContent->getQuantity();

      if ($quantity === 1) {
        $this->addFlash('danger', $translator->trans('alerts.product.minus'));
      } else {
        $quantity -= 1;
        $cartContent->setQuantity($quantity);
        $entityManager->persist($cartContent);
        $entityManager->flush();
      }
      return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);

    }

    #[Route('/{id}', name: 'app_cart_content_delete', methods: ['POST'])]
    public function delete(Request $request, CartContent $cartContent, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cartContent->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($cartContent);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('alerts.product.remove_from_cart'));
        }

        return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
    }
}
