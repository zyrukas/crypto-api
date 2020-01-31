<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class UserAuthenticator
{
    private UserRepository $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string|null $token
     *
     * @return User|null
     */
    public function authenticate(?string $token): ?User
    {
        if ($token === null || \strlen($token) !== 32) {
            return null;
        }

        return $this->userRepository->findOneBy(['token' => $token]);
    }
}
