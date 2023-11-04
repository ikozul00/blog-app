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
    #[Route('/api/likes', name:'addLikeToPost', methods:['POST'])]
    function addLikeToPost(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $data=json_decode($request->getContent(), true);
        $newLike = new Likes();
        $newLike->setUser($this->getUser());
        $newLike->setPost($entityManager->getRepository(Post::class)->find($data['postId']));
        $newLike->setTimestamp(new \DateTime());

        $entityManager->persist($newLike);
        $entityManager->flush();

        return new Response('Saved new like with id '.$newLike->getId());
    }
}