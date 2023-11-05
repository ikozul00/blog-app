<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Favorites;
use App\Entity\Likes;
use App\Entity\Post;
use App\Entity\PostTag;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PostsController extends AbstractController
{

    #[Route('/api/posts',name: 'postsList', methods: ['GET'])]
    function fetchPosts(EntityManagerInterface $entityManager): Response
    {
        $posts=$entityManager->getRepository( Post::class)->getPostsList();
        return new JsonResponse($posts);
    }

    #[Route('/api/posts/{id}', methods: ['GET'])]
    function getPostDetails(EntityManagerInterface $entityManager, string $id):Response
    {
        $data=[];
        $post=$entityManager->getRepository( Post::class)->getPost($id);
        if (!$post[0]) {
            throw $this->createNotFoundException(
                'No post found for id '.$id
            );
        }
        $data['post'] = $post[0];
        $data['tags'] = $entityManager->getRepository(Tag :: class) ->findByPostId($id);
        $data['comments'] = $entityManager->getRepository(Comment::class)->findByPostId($id);
        $isFavorite = $entityManager->getRepository(Favorites::class)->findByUserAndPostId($id, 65);
        if($isFavorite==0){
            $data['ifFavorite']=false;
        }
        else{
            $data['ifFavorite']=true;
        }
        $data['likes']=$entityManager->getRepository(Likes::class)->findByPostId($id);
        // Create a JsonResponse and return it
        return new JsonResponse($data);

    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/posts/create',name: 'createPost', methods: ['POST'])]
    function createPost(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data=json_decode($request->getContent(), true);

        $newPost = new Post();
        $newPost->setTitle($data['title']);
        $newPost->setContent($data['content']);
        $newPost->setCreatedAt(new \DateTime());
        $user = $this->getUser();
        $newPost->setUser($user);
        $entityManager->persist($newPost);
        $entityManager->flush();
        $id = $newPost->getId();

        return new JsonResponse(['id' => $id]);

    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/posts/update',name: 'updatePost', methods: ['PUT'])]
    function updatePost(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data=json_decode($request->getContent(), true);

        $post = $entityManager->getRepository( Post::class)->find($data['id']);

        if(!$post){
            throw $this->createNotFoundException(
                'No post found for id '.$data['id']
            );
        }

        $post->setTitle($data['title'] ?? $post->getTitle());
        $post->setContent($data['content'] ?? $post->getContent());
        $post->setLastEdited(new \DateTime());

        $entityManager->flush();

        return new Response('Updated product with id '.$post->getId());

    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/posts/delete/{id}', name:'deletePost', methods: ['DELETE'])]
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


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/posts/addTag', name:'addTag', methods:['POST'])]
    function addTag(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data=json_decode($request->getContent(), true);
        $post = $entityManager->getRepository(Post::class)->find($data['postId']);
        $tag = $entityManager->getRepository(Tag::class)->find($data['tagId']);
        $isExist = $entityManager->getRepository(PostTag::class) ->count(['post'=>$post, 'tag'=>$tag]);
        if($isExist !== 0){
            return new Response("Exists.");
        }

        $newConnection = new PostTag();

        $newConnection -> setPost($post);
        $newConnection -> setTag($tag);

        $entityManager->persist($newConnection);
        $entityManager->flush();
        return new Response(status: 200);
    }




    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/posts/removeTag', name:'removeTag', methods:['DELETE'])]
    function removeTag(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data=json_decode($request->getContent(), true);

        $numberOfDeleted=$entityManager->getRepository( PostTag::class)->deleteTagFromPost($data['postId'], $data['tagId']);
        if($numberOfDeleted!=1){
            throw $this->createNotFoundException(
                'No data found'
            );
        }
        return new Response(status: 200);
    }

    #[Route('/api/posts/like/{id}', name:'likePost', methods:['POST'])]
    function likePost(Request $request, EntityManagerInterface $entityManager, string $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $data=json_decode($request->getContent(), true);
        $newLike = new Likes();
        $newLike->setPost($entityManager->getRepository(Post::class)->find($id));
        $newLike->setUser($entityManager->getRepository(User::class) ->find($data['userId']));

        $entityManager->persist($newLike);
        $entityManager->flush();
        return new Response(status: 200);
    }



}