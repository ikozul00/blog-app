<?php

namespace App\Controller;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    #[Route('/api/tags', name:'tagsList', methods:['GET'])]
    function getTags(EntityManagerInterface $entityManager): Response
    {
        $tags = $entityManager->getRepository(Tag :: class) ->getTagsList();
        return new JsonResponse($tags);
    }


    #[Route('/api/tags/create', name:'createTag', methods:['POST'])]
    function createTag(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data=json_decode($request->getContent(), true);
        $newTag = new Tag();
        $newTag->setName($data['name']);

        $entityManager->persist($newTag);
        $entityManager->flush();

        return new Response('Saved new tag with id '.$newTag->getId());
    }

    #[Route('/api/tags/update', name:'updateTag', methods:['PUT'])]
    function updateTag(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data=json_decode($request->getContent(), true);

        $tag = $entityManager->getRepository(Tag:: class) ->find($data['id']);
        if(!$tag){
            throw $this->createNotFoundException(
                'No tag found for id '.$data['id']
            );
        }

        $tag->setName($data['name'] ?? $tag->getName());

        $entityManager->flush();

        return new Response('Updated tag with id '.$tag->getId());
    }


    #[Route('/api/tags/delete/{id}', name:'deleteTag', methods:['DELETE'])]
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

    #[Route('/api/tags/post/{id}', name:'postTags', methods:['GET'])]
    function getPostTags(EntityManagerInterface $entityManager, string $id): Response
    {
        $tags = $entityManager->getRepository(Tag :: class) ->findByPostId($id);
        return new JsonResponse($tags);
    }

}