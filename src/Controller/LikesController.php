<?php

namespace App\Controller;

use App\Entity\Likes;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikesController extends AbstractController
{
    #[Route('/likes/{id}', name:'addLikeToPost', methods:['POST'])]
    function addLikeToPost(Request $request, EntityManagerInterface $entityManager, string $id): Response
    {
        $data=json_decode($request->getContent(), true);
        $newLike = new Likes();
        $newLike->setUser($entityManager->getRepository(User::class)->find($data['userId']));
        $newLike->setPost($entityManager->getRepository(Post::class)->find($id));
        $newLike->setTimestamp(new \DateTime());

        $entityManager->persist($newLike);
        $entityManager->flush();

        return new Response('Saved new like with id '.$newLike->getId());
    }
}