<?php

namespace App\Controller;

use App\CommonFunctions;
use App\Entity\Comment;
use App\Entity\Favorites;
use App\Entity\Likes;
use App\Entity\Post;
use App\Entity\PostTag;
use App\Entity\Tag;
use App\Entity\User;
use App\ImageOptimizer;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostsController extends AbstractController
{

    #[Route('/api/posts',name: 'postsList', methods: ['GET'])]
    function fetchPosts(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $pagination): Response
    {
        $query=$entityManager->getRepository( Post::class)->getPostsList();
        $result = $pagination->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            $request->query->getInt('limit', 10) /*limit per page*/
        );
        return new JsonResponse(['postsList'=>$result->getItems(), 'postsCount'=> $result->getTotalItemCount(),
            'currentPageNumber' => $result->getCurrentPageNumber()]);
    }

    #[Route('/api/post/{id}', methods: ['GET'])]
    function getPostDetails(EntityManagerInterface $entityManager,Packages $assets, string $id):Response
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
        $imagePath = $data['post']['imagePath'];
        if($imagePath){
            $data['imageUrl'] = $assets->getUrl('uploads/postImages/' . $imagePath);
        }
        else{
            $data['imageUrl'] ="";
        }

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
    function createPost(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, ImageOptimizer $imageOptimizer, CommonFunctions $commonFunctions): Response
    {
        $newPost = new Post();
        $newPost->setTitle($request->request->get('title'));
        $newPost->setContent($request->request->get('content'));
        $newPost->setCreatedAt(new \DateTime());
        $newPost->setUser($this->getUser());
        $image=$request->files->get('image');
        if($image) {
            $imagePath = $commonFunctions->storeImage($image, $slugger, $imageOptimizer, true);
            $newPost->setImagePath($imagePath);
        }
        $entityManager->persist($newPost);
        $entityManager->flush();
        $id = $newPost->getId();

        return new JsonResponse(['id' => $id]);

    }

    //PUT method would send empty request body, can't find how to send multipart-data with PUT
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/posts/update',name: 'updatePost', methods: ['POST'])]
    function updatePost(Request $request,LoggerInterface $logger, EntityManagerInterface $entityManager, SluggerInterface $slugger, ImageOptimizer $imageOptimizer, CommonFunctions $commonFunctions): Response
    {
        $id = $request->request->get('id');
        $post = $entityManager->getRepository( Post::class)->find($id);

        if(!$post){
            throw $this->createNotFoundException(
                'No post found for id '.$request->request->get('id')
            );
        }

        $post->setTitle($request->request->get('title') ?? $post->getTitle());
        $post->setContent($request->request->get('content') ?? $post->getContent());
        $post->setLastEdited(new \DateTime());
        $image=$request->files->get('image');
        if($image) {
            $imagePath = $commonFunctions->storeImage($image, $slugger, $imageOptimizer, true);
            $post->setImagePath($imagePath);
        }

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