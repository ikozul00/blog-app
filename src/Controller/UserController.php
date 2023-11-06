<?php

namespace App\Controller;

use App\CommonFunctions;
use App\Entity\Comment;
use App\Entity\Favorites;
use App\Entity\Likes;
use App\Entity\User;
use App\ImageOptimizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends AbstractController
{
    #[Route('/api/profile/{id}',name: 'fetchUser', methods: ['GET'])]
    #[IsGranted('owner', subject: 'user')]
    function fetchUser(EntityManagerInterface $entityManager, User $user, Packages $assets): Response
    {
        $id=$user->getId();
        $imagePath = $user->getImagePath();
        if($imagePath){
            $imageUrl = $assets->getUrl('uploads/userImages/' . $imagePath);
        }
        else{
            $imageUrl ="";
        }
        try {
            $userJson = ['id' => $id, 'email' => $user->getEmail(), 'username' => $user->getUsername(), 'roles' => $user->getRoles(), 'image' => $imageUrl];
            $comments = $entityManager->getRepository(Comment::class)->findByUserId($id);
            $favorites = $entityManager->getRepository(Favorites:: class)->findByUserId($id);
            $likes = $entityManager->getRepository(Likes:: class)->findByUserId($id);
            $data = ['user' => $userJson, 'comments' => $comments, 'favorites' => $favorites, 'likes' => $likes];
        }
        catch(\Exception $error){
            return new JsonResponse(['error' => 'Error happened while getting user info.'.$error], 500);
        }
        return new JsonResponse($data);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/profiles',name: 'fetchUsers', methods: ['GET'])]
    function fetchUsers(EntityManagerInterface $entityManager): Response
    {
        try {
            $users = $entityManager->getRepository(User::class)->getUsersList();
        }
        catch(\Exception $error){
            return new JsonResponse(['error' => 'Error happened while getting list of users.'.$error], 500);
        }
        return new JsonResponse($users);
    }


    #[Route('/api/profile/{id}',name: 'updateUser', methods: ['POST'])]
    #[IsGranted('owner', subject: 'user')]
    function updateUser(User $user, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher,
                        SluggerInterface $slugger, ImageOptimizer $imageOptimizer, CommonFunctions $commonFunctions): Response
    {
        if(!$user){
            throw $this->createNotFoundException(
                'No user found for id '.$request->request->get('id')
            );
        }
        //check if old password is ok
        if($request->request->get('password') !== "" && !$passwordHasher->isPasswordValid($user, $request->request->get('oldPassword'))){
            return new JsonResponse(['message'=>'Wrong password.'],400);
        }
        //check if user with new email already exists
        if($request->request->get('email') !== $user->getEmail()) {
            $isExist = $entityManager->getRepository(User::class)->count(['email' => $request->request->get('email')]);
            if ($isExist !== 0) {
                return new JsonResponse(['message' => "User exists."], 400);
            }
        }

        try {
            $user->setEmail($request->request->get('email') ?? $user->getEmail());
            $user->setUsername($request->request->get('username') ?? $user->getUsername());
            if ($request->request->get('password') !== "") {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $request->request->get('password')
                );
                $user->setPassword($hashedPassword);
            }
            $image = $request->files->get('image');
            if ($image) {
                $imagePath = $commonFunctions->storeImage($image, $slugger, $imageOptimizer, false);
                $user->setImagePath($imagePath);
            }
            $userData = $user;
            $entityManager->flush();
        }
        catch(\Exception $error){
            return new JsonResponse(['error' => 'Error happened while updating user info.'.$error], 500);
        }

        return new JsonResponse($userData);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/profile/{id}', name:'deleteUser', methods: ['DELETE'])]
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