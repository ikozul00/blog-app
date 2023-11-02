<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{

    #[Route('/posts',name: 'postsList', methods: ['GET'])]
    function getPost(EntityManagerInterface $entityManager):Response
    {
        $posts=$entityManager->getRepository( Post::class)->getPostsList();
        return new JsonResponse($posts);
        //return $this->render("blog/posts.html.twig", ['posts' => $posts]);
    }

    #[Route('/posts/{id}')]
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

    //TODO: Change method to DELETE
    #[Route('/posts/delete/{id}', name:'deletePost', methods: ['GET'])]
    function deletePost(EntityManagerInterface $entityManager, string $id){
        $numberOfPosts=$entityManager->getRepository( Post::class)->deletePost($id);
        if($numberOfPosts==0){
            throw $this->createNotFoundException(
                'No post found for id '.$id
            );
        }
        //TODO: return some response
    }
}