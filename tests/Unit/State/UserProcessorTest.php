<?php

namespace App\Tests\Unit\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\State\UserProcessor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use TypeError;

class UserProcessorTest extends TestCase
{
    private UserProcessor $userProcessor;

    protected function setUp(): void
    {
        $this->userProcessor = new UserProcessor(
            $this->createMock(ProcessorInterface::class),
            $this->createMock(UserPasswordHasherInterface::class),
        );
    }

    public function testConstructor(): void
    {
        /** @phpstan-ignore-next-line */
        $this->assertInstanceOf(UserProcessor::class, $this->userProcessor);
    }

    public function testProcessSuccessful(): void
    {
        $user = (new User())->setPlainPassword('123456');
        $this->userProcessor->process(
            $user,
            $this->createMock(Operation::class),
        );

        $this->assertNull($this->userProcessor->getUser()->getPlainPassword());
        $this->assertNotNull($this->userProcessor->getUser()->getPassword());
    }

    public function testProcessFail(): void
    {
        $this->expectException(TypeError::class);

        $this->userProcessor->process(
            123,
            $this->createMock(Operation::class),
        );
    }
}
