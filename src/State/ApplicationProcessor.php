<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Application;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use TypeError;

class ApplicationProcessor implements ProcessorInterface
{
    protected Application $application;

    public function __construct(
        private readonly ProcessorInterface $processor,
        private readonly Security $security
    ) {
    }

    /** @phpstan-ignore-next-line */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $this->validateAndSetApplication($data);
        $this->mutate();
        return $this->processor->process($this->getApplication(), $operation, $uriVariables, $context);
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    private function validateAndSetApplication(mixed $data): void
    {
        if (!($data instanceof Application)) {
            throw new \TypeError(
                sprintf('data must be instanceof %s', Application::class)
            );
        }

        $this->setApplication($data);
    }

    private function mutate(): void
    {
        $this->application->setOwner($this->getUser());
        $date = new \DateTimeImmutable();

        if (!$this->application->getDateCreate()) {
            $this->application->setDateCreate($date);
        }

        $this->application->setDateUpdate($date);
    }

    private function setApplication(Application $application): void
    {
        $this->application = $application;
    }

    private function getUser(): User
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new TypeError(
                sprintf(
                    'Expected App\\Entity\\User, got %s',
                    $user === null ? 'null' : get_class($user)
                )
            );
        }
        return $user;
    }
}
