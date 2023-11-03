<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{

    #[Route('/posts',name: 'postsList', methods: ['GET'])]
    function fetchPosts(EntityManagerInterface $entityManager): Response
    {
        $posts=$entityManager->getRepository( Post::class)->getPostsList();
        return new JsonResponse($posts);
    }

    #[Route('/posts/{id}', methods: ['GET'])]
    function getPostDetails(EntityManagerInterface $entityManager, string $id):Response
    {
        $post=$entityManager->getRepository( Post::class)->find($id);
        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id '.$id
            );
        }
        // Create a JsonResponse and return it
        return new Response($post);

    }

    #Route['/posts/create', name:'createPost, methods: ['POST']
    function createPost(Request $request, EntityManagerInterface $entityManager): Response
    {
        $request->request->get(title);
    }


    #[Route('/posts/delete/{id}', name:'deletePost', methods: ['DELETE'])]
    function deletePost(EntityManagerInterface $entityManager, string $id): Response
    {
        $numberOfPosts=$entityManager->getRepository( Post::class)->deletePost($id);
        if($numberOfPosts==0){
            throw $this->createNotFoundException(
                'No post found for id '.$id
            );
        }
        return new Response(status: 200);
    }
}