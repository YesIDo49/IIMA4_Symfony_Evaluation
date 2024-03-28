<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/account', name: 'app_cart_account', methods: ['GET'])]
    public function account(CartRepository $cartRepository): Response
    {
        return $this->render('user/account.html.twig', [
            'carts' => $cartRepository->createQueryBuilder('c')
                ->where('c.state = 1')
                ->orderBy('c.purchase_date', 'DESC')
                ->getQuery()
                ->getResult()
        ]);
    }

    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/super-admin', name: 'app_super_admin_index', methods: ['GET'])]
    public function superAdminIndex(UserRepository $userRepository, CartRepository $cartRepository): Response
    {
        return $this->render('user/super-admin-index.html.twig', [
            'users' => $userRepository->createQueryBuilder('u')
                ->orderBy('u.registration_date', 'DESC')
                ->getQuery()
                ->getResult(),
            'carts' => $cartRepository->createQueryBuilder('c')
                ->where('c.state = 1')
                ->orderBy('c.purchase_date', 'DESC')
                ->getQuery()
                ->getResult()
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cart_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
