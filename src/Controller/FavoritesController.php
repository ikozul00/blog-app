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
    #[Route('/api/addToFavorites', name:'addPostToFavorites', methods:['POST'])]
    function addToFavorites(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $data=json_decode($request->getContent(), true);
        $newFavorite = new Favorites();
        $newFavorite->setUser($this->getUser());
        $newFavorite->setPost($entityManager->getRepository(Post::class)->find($data['postId']));

        $entityManager->persist($newFavorite);
        $entityManager->flush();

        return new Response('Saved new favorite with id '.$newFavorite->getId());
    }

    #[Route('/api/removeFromFavorites/{postId}', name:'removeFavorite', methods:['DELETE'])]
    function removeFromFavorites(Request $request, EntityManagerInterface $entityManager, string $postId): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $user = $this->getUser();
        $numberOfDeleted = $entityManager->getRepository(Favorites::class) -> deletePostFromFavorites($postId, $user->getUserIdentifier());

        if($numberOfDeleted==0){
            throw $this->createNotFoundException(
                'No data found for ids '
            );
        }
        return new Response(status: 200);
    }
}