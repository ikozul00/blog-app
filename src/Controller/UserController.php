<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Favorites;
use App\Entity\Likes;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/api/user/{id}',name: 'getUser', methods: ['GET'])]
    function fetchPosts(EntityManagerInterface $entityManager, string $id): Response
    {
        $user=$entityManager->getRepository( User::class)->find($id);
        $userJson = ['id' => $user->getId(), 'email' => $user ->getEmail(), 'username' => $user->getUsername()];
        $comments = $entityManager -> getRepository(Comment::class) -> findByUserId($id);
        $favorites = $entityManager -> getRepository(Favorites:: class) ->findByUserId($id);
        $likes = $entityManager -> getRepository(Likes:: class) ->findByUserId($id);
        $data=['user' => $userJson, 'comments' => $comments, 'favorites' => $favorites, 'likes' => $likes];
        return new JsonResponse($data);
    }
}