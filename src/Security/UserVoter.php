<?php

namespace App\Security;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public function __construct(
        private Security $security,
    ){
    }

    const OWNER = 'owner';

    protected function supports(string $attribute, mixed $subject): bool{
        if (!$attribute === self::OWNER) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool{
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // you know $subject is a User object, thanks to `supports()
        //`
        /** @var User $requestUser */
        $requestUser = $subject;

        return match($attribute) {
            self::OWNER => $this->isAdminOrOwner($requestUser, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }
    private function isAdminOrOwner(User $requestUser, User $user): bool
    {
        if(in_array('ROLE_ADMIN', $user->getRoles())){
            return true;
        }
        return $user === $requestUser;
    }

}