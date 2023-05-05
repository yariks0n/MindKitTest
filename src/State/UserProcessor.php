<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProcessor implements ProcessorInterface
{
    protected User $user;

    public function __construct(
        private readonly ProcessorInterface $processor,
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    /** @phpstan-ignore-next-line */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $this->validateAndSetUser($data);
        $this->mutateUser();

        return $this->processor()->process($this->user, $operation, $uriVariables, $context);
    }

    private function validateAndSetUser(mixed $data): void
    {
        if (!($data instanceof User)) {
            throw new \TypeError(
                sprintf('data must be instanceof %s', User::class)
            );
        }

        $this->setUser($data);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    private function mutateUser(): void
    {
        if ($this->user->getPlainPassword()) {
            $hashedPassword = $this->hasher->hashPassword(
                $this->user,
                $this->user->getPlainPassword()
            );
            $this->user->setPassword($hashedPassword);
            $this->user->eraseCredentials();
        }
    }

    private function setUser(User $user): void
    {
        $this->user = $user;
    }

    private function processor(): ProcessorInterface
    {
        return $this->processor;
    }
}
