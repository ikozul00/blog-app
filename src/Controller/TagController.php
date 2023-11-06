<?php

namespace App\Controller;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TagController extends AbstractController
{
    #[Route('/api/tags', name:'tagsList', methods:['GET'])]
    function getTags(EntityManagerInterface $entityManager): Response
    {
        $tags = $entityManager->getRepository(Tag :: class) ->getTagsList();
        return new JsonResponse($tags);
    }


    #[Route('/api/tag', name:'createTag', methods:['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    function createTag(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data=json_decode($request->getContent(), true);
        $newTag = new Tag();
        $newTag->setName($data['name']);

        $entityManager->persist($newTag);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Saved new tag with id '.$newTag->getId()],201);
    }

    #[Route('/api/tag', name:'updateTag', methods:['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    function updateTag(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data=json_decode($request->getContent(), true);

        $tag = $entityManager->getRepository(Tag:: class) ->find($data['id']);
        if(!$tag){
            throw $this->createNotFoundException(
                'No tag found for id '.$data['id']
            );
        }
        if($data['name'] === $tag->getName()){
            return new JsonResponse(['message' => 'Enter new tag name.'], 400);
        }
        $tag->setName($data['name'] ?? $tag->getName());

        $entityManager->flush();

        return new JsonResponse(['message' => 'Updated tag with id '.$tag->getId()], 200);
    }


    #[Route('/api/tag/{id}', name:'deleteTag', methods:['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    function deleteTag(EntityManagerInterface $entityManager, string $id): Response
    {
       $numberOfDeleted = $entityManager->getRepository(Tag::class) -> deleteTag($id);

        if($numberOfDeleted==0){
            throw $this->createNotFoundException(
                'No tag found for id '.$id
            );
        }
        return new Response(status: 200);
    }

    #[Route('/api/tag/post/{id}', name:'postTags', methods:['GET'])]
    function getPostTags(EntityManagerInterface $entityManager, string $id): Response
    {
        $tags = $entityManager->getRepository(Tag :: class) ->findByPostId($id);
        return new JsonResponse($tags);
    }

}