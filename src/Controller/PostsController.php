<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    #[Route('/')]
    function getPost(EntityManagerInterface $entityManager):Response
    {
        $posts=$entityManager->getRepository( Post::class)->findAll();
        return $this->json($posts);
    }
}