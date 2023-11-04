<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login')]
    public function login(#[CurrentUser] ?User $user): Response
    {
        if ($user === null) {
            return new JsonResponse(['message' => 'missing credentials'], Response::HTTP_UNAUTHORIZED);
        }
        return new JsonResponse(['id' => $user->getId(), 'email' => $user->getEmail(), 'role' => $user->getRoles()]);
    }

    #[Route('/api/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
    }
}