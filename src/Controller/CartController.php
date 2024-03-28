<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Form\CartType;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\CartContent;
use App\Entity\Product;


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
          return $this->render('cart/index.html.twig', [
            'cart' => $cart
          ]);
        }
    }

    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/all', name: 'app_cart_show_all', methods: ['GET'])]
    public function showAll(CartRepository $cartRepository): Response
    {
      return $this->render('cart/index.html.twig', [
        'carts' => $cartRepository->createQueryBuilder('c')
          ->where('c.state = 0')
          ->orderBy('c.purchase_date', 'DESC')
          ->getQuery()
          ->getResult()
      ]);
    }

    #[Route('/new', name: 'app_cart_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
      $cart = new Cart();
      $cart->setState(false);
      $cart->setUser($this->getUser());

      $entityManager->persist($cart);
      $entityManager->flush();

//      return $this->redirectToRoute('app_cart_show', ["id" => $cart->getId()], Response::HTTP_SEE_OTHER);
      return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_cart_show', methods: ['GET'])]
    public function show(Cart $cart, EntityManagerInterface $entityManager): Response
    {
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
          'total' => $total
      ]);
    }

    #[Route('/{id}/edit', name: 'app_cart_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {
      $form = $this->createForm(CartType::class, $cart);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid() && !$cart->isState()) {
          $entityManager->flush();

          return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
      }

      return $this->render('cart/edit.html.twig', [
          'cart' => $cart,
          'form' => $form,
      ]);
    }

    #[Route('/{id}/pay', name: 'app_cart_pay', methods: ['GET', 'POST'])]
    public function pay(Cart $cart, EntityManagerInterface $entityManager): Response
    {
      $cart->setState(true);
      $cart->setPurchaseDate(new \DateTime("now", new \DateTimeZone("Europe/Paris")));

      $entityManager->flush();

      $this->addFlash('success', 'Cart paid successfully');

      return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_cart_delete', methods: ['POST'])]
    public function delete(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {
      if ($this->isCsrfTokenValid('delete'.$cart->getId(), $request->getPayload()->get('_token'))) {
          $entityManager->remove($cart);
          $entityManager->flush();
      }

      return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
    }
}