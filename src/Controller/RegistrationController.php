<?php

namespace App\Controller;

use App\CommonFunctions;
use App\Entity\User;
use App\ImageOptimizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/api/registration',name: 'register', methods: ['POST'])]
    public function index(UserPasswordHasherInterface $passwordHasher,
                          Request $request, EntityManagerInterface $entityManager, CommonFunctions $commonFunctions,
                          SluggerInterface $slugger, ImageOptimizer $imageOptimizer ): Response
    {

        $isExist = $entityManager->getRepository(User::class) ->count(['email'=>$request->request->get('email')]);
        if($isExist !== 0){
            return new Response("User exists.");
        }
        $user = new User();
        $user->setEmail($request->request->get('email'));
        $user->setUsername($request->request->get('username'));

        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $request->request->get('password')
        );
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);
        $entityManager->persist($user);
        $entityManager->flush();
        $image=$request->files->get('image');
        if($image) {
            $imagePath = $commonFunctions->storeImage($image, $slugger, $imageOptimizer, false);
            $user->setImagePath($imagePath);
        }

        return new Response("User registered.");

    }
}