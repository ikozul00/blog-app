<?php

namespace App\Security;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVoter extends Voter
{

    public function __construct(
        private Security $security,
    ){
    }

    const DELETE = 'delete';
    protected function supports(string $attribute, mixed $subject): bool{
        if (!$attribute === self::DELETE) {
            return false;
        }

        if (!$subject instanceof Comment) {
            return false;
        }

        return true;
    }
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool{
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // you know $subject is a Post object, thanks to `supports()
        //`
        /** @var Comment $comment */
        $comment = $subject;

        return match($attribute) {
            self::DELETE => $this->canDelete($comment, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }
    private function canDelete(Comment $comment, User $user): bool
    {
        if(in_array('ROLE_ADMIN', $user->getRoles())){
            return true;
        }
        return $user === $comment->getUser();
    }
}