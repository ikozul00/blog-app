<?php

namespace App\Faker\Provider;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProviderFactory
{

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function createUserProvider(User $user): UserProvider
    {
        return new UserProvider($this->passwordHasher, $user);
    }

}