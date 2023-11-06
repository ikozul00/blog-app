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
use Symfony\Contracts\Translation\TranslatorInterface;

class PostsController extends AbstractController
{
    //fetch labels to be displayed on frontend, in appropriate locale
    #[Route('/api/{_locale}/posts-page', name:'getPostsPage', methods:['GET'])]
    public function translatePostsPageParameters(Request $request, TranslatorInterface $translator): Response
    {
        $locale = $request->getLocale();
        $title = $translator->trans('posts.title',[], 'messages', $locale);
        $more = $translator->trans('posts.more',[], 'messages', $locale);
        $add = $translator->trans('posts.add',[], 'messages', $locale);
        $search = $translator->trans('posts.search',[], 'messages', $locale);
        return new JsonResponse([ 'title' => $title, 'more' => $more, 'add' => $add, 'search' => $search]);
    }

    //fetch labels to be displayed on frontend, in appropriate locale
    #[Route('/api/{_locale}/post-page', name:'getPostPage', methods:['GET'])]
    public function translatePostPageParameters(Request $request, TranslatorInterface $translator): Response
    {
        $locale = $request->getLocale();
        $tagAdd = $translator->trans('post.tagAdd',[], 'messages', $locale);
        $like = $translator->trans('post.like',[], 'messages', $locale);
        $favorite = $translator->trans('post.favorite',[], 'messages', $locale);
        $favoriteRemove = $translator->trans('post.favoriteRemove',[], 'messages', $locale);
        $delete = $translator->trans('post.delete',[], 'messages', $locale);
        $update = $translator->trans('post.update',[], 'messages', $locale);
        $comments = $translator->trans('post.comments',[], 'messages', $locale);
        return new JsonResponse([ 'tagAdd' => $tagAdd, 'like' => $like, 'favorite' => $favorite,
            'favoriteRemove' => $favoriteRemove,'delete' => $delete, 'update' => $update, 'comments' => $comments]);
    }

    #[Route('/api/posts',name: 'fetchPosts', methods: ['GET'])]
    function fetchPosts(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $pagination): Response
    {
        try{
            $filter = $request->query->get('filter', "");
            if(!$filter)
            {
                $query=$entityManager->getRepository( Post::class)->getPostsList();
            }
            else{
                $query=$entityManager->getRepository( Post::class)->getPostsListWithFilter('%'.$filter.'%');
            }

            $result = $pagination->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                $request->query->getInt('limit', 10) /*limit per page*/
            );
        }
        catch(\Exception $error){
            return new JsonResponse(['error' => 'An error occurred while fetching posts.'.$error], 500);
        }
        return new JsonResponse(['postsList'=>$result->getItems(), 'postsCount'=> $result->getTotalItemCount(),
            'currentPageNumber' => $result->getCurrentPageNumber()]);
    }

    #[Route('/api/post/{id}', name:'fetchPost', methods: ['GET'])]
    function fetchPost(EntityManagerInterface $entityManager,Packages $assets, string $id):Response
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
        $user = $this->getUser();
        $userData = $entityManager->getRepository(User::class) ->findBy(['email' => $user->getUserIdentifier()]);
        $isFavorite = $entityManager->getRepository(Favorites::class)->findByUserAndPostId($id, $userData[0]->getId());
        if($isFavorite==0){
            $data['isFavoriteData']=false;
        }
        else{
            $data['isFavoriteData']=true;
        }
        $data['likes']=$entityManager->getRepository(Likes::class)->findByPostId($id);
        // Create a JsonResponse and return it
        return new JsonResponse($data);

    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/post',name: 'createPost', methods: ['POST'])]
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
        try {
            $entityManager->persist($newPost);
            $entityManager->flush();
            $id = $newPost->getId();
        }
        catch(\Exception $error){
            return new JsonResponse(['error' => 'Error happened while adding post to db.'.$error], 500);
        }

        return new JsonResponse(['id' => $id], 201);

    }

    //PUT method would send empty request body, can't find how to send multipart-data with PUT
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/post-update',name: 'updatePost', methods: ['POST'])]
    function updatePost(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger,
                        ImageOptimizer $imageOptimizer, CommonFunctions $commonFunctions): Response
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
            $imagePath = $commonFunctions->storeImage($image, $slugger, $imageOptimizer,true, );
            $post->setImagePath($imagePath);
        }

        try {
            $entityManager->flush();
        }
        catch(\Exception $error){
            return new JsonResponse(['error' => 'Error happened while updating post in db.'.$error], 500);
        }

        return new JsonResponse(['message' => 'Updated product with id '.$post->getId()], 200);

    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/post/{id}', name:'deletePost', methods: ['DELETE'])]
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
    #[Route('/api/post/tag', name:'addTagToPost', methods:['POST'])]
    function addTagToPost(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data=json_decode($request->getContent(), true);
        $post = $entityManager->getRepository(Post::class)->find($data['postId']);
        $tag = $entityManager->getRepository(Tag::class)->find($data['tagId']);
        $isExist = $entityManager->getRepository(PostTag::class) ->count(['post'=>$post, 'tag'=>$tag]);
        if($isExist !== 0){
            return new JsonResponse(['message' => 'Exists.'], 400);
        }

        $newRelation = new PostTag();

        $newRelation -> setPost($post);
        $newRelation -> setTag($tag);
        try {
            $entityManager->persist($newRelation);
            $entityManager->flush();
        }
        catch(\Exception $error){
            return new JsonResponse(['error' => 'Error happened while adding tag to post.'.$error], 500);
        }

        return new Response(status: 201);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/post/tag', name:'removeTag', methods:['DELETE'])]
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


}