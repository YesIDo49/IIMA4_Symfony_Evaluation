<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\CartContent;
use App\Entity\Product;
use Symfony\Contracts\Translation\TranslatorInterface;


#[IsGranted('ROLE_USER')]
#[Route('/cart')]
class CartController extends AbstractController
{

    #[Route('/', name: 'app_cart_index', methods: ['GET'])]
    public function index(CartRepository $cartRepository): Response
    {
        $cart = $cartRepository->findOneBy(
          ['user' => $this->getUser(), 'state' => false],
          ['id' => 'DESC']
        );

        if (!$cart) {
          return $this->redirectToRoute('app_cart_new', [], Response::HTTP_SEE_OTHER);
        } else {
          return $this->redirectToRoute('app_cart_show', ["id" => $cart->getId()], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/new', name: 'app_cart_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
      $cart = new Cart();
      $cart->setState(false);
      $cart->setUser($this->getUser());

      $entityManager->persist($cart);
      $entityManager->flush();

      return $this->redirectToRoute('app_cart_show', ["id" => $cart->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_cart_show', methods: ['GET'])]
    public function show(Cart $cart, EntityManagerInterface $entityManager): Response
    {
      $user = $this->getUser();
      $cartContents = $entityManager->getRepository(CartContent::class)->findBy(['cart' => $cart]);
      $total = 0;

      foreach ($cartContents as $cartContent) {
          $product = $cartContent->getProduct();
          if ($product instanceof Product) {
              $total += $product->getPrice() * $cartContent->getQuantity();
          }
      }

      return $this->render('cart/show.html.twig', [
          'cart' => $cart,
          'cartContents' => $cartContents,
          'total' => $total,
          'currentUser' => $user
      ]);
    }

    #[Route('/{id}/pay', name: 'app_cart_pay', methods: ['GET', 'POST'])]
    public function pay(Cart $cart, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
      $cart->setState(true);
      $cart->setPurchaseDate(new \DateTime("now", new \DateTimeZone("Europe/Paris")));

      $cartContents = $cart->getCartContents();

      foreach ($cartContents as $cartContent) {
          $product = $cartContent->getProduct();
          if ($product instanceof Product) {
              $product->setStock($product->getStock() - $cartContent->getQuantity());
              $entityManager->persist($product);
          }
      }

      $entityManager->flush();

      $this->addFlash('success', $translator->trans('alerts.cart.pay'));

      return $this->redirectToRoute('app_cart_account', [], Response::HTTP_SEE_OTHER);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_cart_delete', methods: ['POST'])]
    public function delete(Request $request, Cart $cart, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
      if ($this->isCsrfTokenValid('delete'.$cart->getId(), $request->getPayload()->get('_token'))) {
          $entityManager->remove($cart);
          $entityManager->flush();
      }

      $this->addFlash('success', $translator->trans('alerts.cart.clear'));

      return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
    }
}