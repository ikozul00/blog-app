<?php

namespace App\Controller;

use App\Entity\Favorites;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavoritesController extends AbstractController
{
    #[Route('/api/favorite', name:'addPostToFavorites', methods:['POST'])]
    function addPostToFavorites(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $data=json_decode($request->getContent(), true);
        $user = $this->getUser();
        $userData = $entityManager->getRepository(User::class) ->findBy(['email' => $user->getUserIdentifier()]);
        $post = $entityManager->getRepository(Post::class)->find($data['postId']);
        $isFavorite = $entityManager->getRepository(Favorites::class)->findByUserAndPostId($userData[0]->getId(), $userData[0]->getId());
        if($isFavorite!=0){
            return new JsonResponse(['message' => 'Already is in favorites.'], 400);
        }
        try{

            $newFavorite = new Favorites();
            $newFavorite->setUser($user);
            $newFavorite->setPost($post);
            $entityManager->persist($newFavorite);
            $entityManager->flush();
        }
        catch(\Exception $error){
            return new JsonResponse(['error' => 'Error happened while adding post to favorites.'.$error], 500);
        }
        return new JsonResponse(['message' => 'Saved new favorite with id '.$newFavorite->getId()], 201);
    }

    #[Route('/api/favorite/{postId}', name:'removePostFromFavorites', methods:['DELETE'])]
    function removePostFromFavorites(Request $request, EntityManagerInterface $entityManager, string $postId): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();
        $userData = $entityManager->getRepository(User::class) ->findBy(['email' => $user->getUserIdentifier()]);
        $numberOfDeleted = $entityManager->getRepository(Favorites::class) -> deletePostFromFavorites($postId, $userData[0]->getId());

        if($numberOfDeleted==0){
            throw $this->createNotFoundException(
                'No data found for ids '
            );
        }
        return new Response(status: 200);
    }
}