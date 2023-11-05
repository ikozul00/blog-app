<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Favorites;
use App\Entity\Likes;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/api/profile/{id}',name: 'getUser', methods: ['GET'])]
    #[IsGranted('owner', subject: 'user')]
    function fetchUser(EntityManagerInterface $entityManager, User $user): Response
    {
        $id=$user->getId();
        $userJson = ['id' => $user->getId(), 'email' => $user ->getEmail(), 'username' => $user->getUsername()];
        $comments = $entityManager -> getRepository(Comment::class) -> findByUserId($id);
        $favorites = $entityManager -> getRepository(Favorites:: class) ->findByUserId($id);
        $likes = $entityManager -> getRepository(Likes:: class) ->findByUserId($id);
        $data=['user' => $userJson, 'comments' => $comments, 'favorites' => $favorites, 'likes' => $likes];
        return new JsonResponse($data);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/profiles',name: 'getUsers', methods: ['GET'])]
    function fetchUsers(EntityManagerInterface $entityManager): Response
    {
        $users=$entityManager->getRepository( User::class)->getUsersList();
        return new JsonResponse($users);
    }


    #[Route('/api/profile/update/{id}',name: 'updateProfile', methods: ['PUT'])]
    #[IsGranted('owner', subject: 'user')]
    function updatePost(User $user, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $data=json_decode($request->getContent(), true);

        if(!$user){
            throw $this->createNotFoundException(
                'No user found for id '.$data['id']
            );
        }
        //check if old password is ok
        if($data['password'] !== "" && !$passwordHasher->isPasswordValid($user, $data['oldPassword'])){
            return new Response('Wrong password.');
        }
        //check if user with new email already exists
        $isExist = $entityManager->getRepository(User::class) ->count(['email'=>$data['email']]);
        if($isExist !== 0){
            return new Response("User exists.");
        }

        $user->setEmail($data['email'] ?? $user->getEmail());
        $user->setUsername($data['username'] ?? $user->getUsername());
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $data['password']
        );
        $user->setPassword($hashedPassword);

        $entityManager->flush();

        return new Response('Updated user with id '.$user->getId());
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/profile/delete/{id}', name:'deleteUser', methods: ['DELETE'])]
    function deleteUser(EntityManagerInterface $entityManager, string $id): Response
    {
        $numberOfDeleted=$entityManager->getRepository( USer::class)->deleteUser($id);
        if($numberOfDeleted==0){
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }
        return new Response(status: 200);
    }
}