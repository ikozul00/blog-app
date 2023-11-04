<?php

namespace App\Controller;

use App\Entity\Favorites;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavoritesController extends AbstractController
{
    #[Route('/api/addToFavorites/{id}', name:'addPostToFavorites', methods:['POST'])]
    function addToFavorites(Request $request, EntityManagerInterface $entityManager, string $id): Response
    {
        $data=json_decode($request->getContent(), true);
        $newFavorite = new Favorites();
        $newFavorite->setUser($entityManager->getRepository(User::class)->find($data['userId']));
        $newFavorite->setPost($entityManager->getRepository(Post::class)->find($id));

        $entityManager->persist($newFavorite);
        $entityManager->flush();

        return new Response('Saved new favorite with id '.$newFavorite->getId());
    }

    #[Route('/api/removeFromFavorites/{postId}/{userId}', name:'removeFavorite', methods:['DELETE'])]
    function removeFromFavorites(Request $request, EntityManagerInterface $entityManager, string $userId, string $postId): Response
    {
        $data=json_decode($request->getContent(), true);
        $numberOfDeleted = $entityManager->getRepository(Favorites::class) -> deletePostFromFavorites($postId, $userId);

        if($numberOfDeleted==0){
            throw $this->createNotFoundException(
                'No data found for ids '
            );
        }
        return new Response(status: 200);
    }
}