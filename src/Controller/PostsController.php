<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController
{
    #[Route('/')]
    function getPost():Response
    {
        return new Response("Message new");
    }
}