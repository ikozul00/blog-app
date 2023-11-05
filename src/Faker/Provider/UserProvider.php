<?php

namespace App\Faker\Provider;

use App\Entity\User;
use Faker\Provider\Base;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserProvider extends Base
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function hashedPassword( User $user=null, string $salt = null): string
    {
        return $this->passwordHasher->hashPassword($user, "12345");
    }
}