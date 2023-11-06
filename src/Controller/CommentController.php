<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommentController extends AbstractController
{
    #[Route('/api/comment',name: 'createComment', methods: ['POST'])]
    function createComment(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        try {
            $data = json_decode($request->getContent(), true);

            $newComment = new Comment();
            $newComment->setContent($data['content']);
            $newComment->setCreatedAt(new \DateTime());
            $user = $this->getUser();
            $newComment->setUser($user);
            $newComment->setPost($entityManager->getRepository(Post::class)->find($data['postId']));
            $entityManager->persist($newComment);
            $entityManager->flush();
        }
        catch(\Exception $error){
            return new JsonResponse(['error' => 'Error happened while creating comment.'.$error], 500);
        }

        // TODO: send email on new comment to admin, this code should work, but I don't have SMTP server or configurated provider
//        $users = $entityManager->getRepository(User::class) -> findAll();
//        $admins = array_filter($users, function(User $user) {
//            return in_array('ROLE_ADMIN', $user->getRoles());
//        });
//        $email = (new Email())
//            ->from('ivana.kozul5@gmail.com')
//            ->to($admins[0]->getEmail())
//            ->subject('New comment')
//            ->text('New comment by user '.$user->getUserIdentifier().' with content '.$data['content']);
//        $mailer->send($email);


        return new JsonResponse(['commentId' => $newComment->getId(), 'content' => $newComment->getContent(),
            'email'=> $user->getUserIdentifier(),'createdAt'=>$newComment->getCreatedAt()], 201);
    }

    #[Route('/api/comment/{id}', name:'deleteComment', methods: ['DELETE'])]
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