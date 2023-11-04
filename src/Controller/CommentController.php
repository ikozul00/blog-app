<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommentController extends AbstractController
{

    #[Route('/api/comment/create',name: 'createComment', methods: ['POST'])]
    function createComment(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $data=json_decode($request->getContent(), true);

        $newComment = new Comment();
        $newComment->setContent($data['content']);
        $newComment->setCreatedAt(new \DateTime());
        $user = $this->getUser();
        $newComment->setUser($user);
        $newComment->setPost($entityManager->getRepository(Post::class)->find($data['postId']));
        $entityManager->persist($newComment);
        $entityManager->flush();

        return new JsonResponse(['commentId' => $newComment->getId(), 'content' => $newComment->getContent(),
            'email'=> $user->getUserIdentifier(),'createdAt'=>$newComment->getCreatedAt()]);
    }

    #[Route('/api/comment/delete/{id}', name:'deleteComment', methods: ['DELETE'])]
    #[IsGranted('delete', subject: 'comment')]
    function deleteComment(Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $numberOfDeleted=$entityManager->getRepository( Comment::class)->deleteComment($comment->getId());
        if($numberOfDeleted==0){
            throw $this->createNotFoundException(
                'No comment found for id '.$comment->getId()
            );
        }
        return new Response(status: 200);
    }

}