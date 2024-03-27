<?php

namespace App\Controller;

use App\Entity\CartContent;
use App\Form\CartContentType;
use App\Repository\CartContentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cartContent = new CartContent();
        $form = $this->createForm(CartContentType::class, $cartContent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cartContent);
            $entityManager->flush();

            return $this->redirectToRoute('app_cart_content_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cart_content/new.html.twig', [
            'cart_content' => $cartContent,
            'form' => $form,
        ]);
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
