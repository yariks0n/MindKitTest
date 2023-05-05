<?php

namespace App\Tests\Unit\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Application;
use App\Entity\User;
use App\State\ApplicationProcessor;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use TypeError;

class ApplicationProcessorTest extends TestCase
{
    private ApplicationProcessor $applicationProcessor;

    protected function setUp(): void
    {
        $security = $this->createStub(Security::class);
        $security->method('getUser')->willReturn(new User());

        $this->applicationProcessor = new ApplicationProcessor(
            $this->createMock(ProcessorInterface::class),
            $security,
        );
    }

    public function testConstructor(): void
    {
        /** @phpstan-ignore-next-line */
        $this->assertInstanceOf(ApplicationProcessor::class, $this->applicationProcessor);
    }

    public function testProcessDateCreateSuccessful(): void
    {
        $application = new Application();
        $this->applicationProcessor->process(
            $application,
            $this->createMock(Operation::class),
        );

        $this->assertNotNull($this->applicationProcessor->getApplication()->getDateCreate());
        $this->assertNotNull($this->applicationProcessor->getApplication()->getDateUpdate());
    }

    public function testProcessUpdateDateCreateSuccessful(): void
    {
        $application = (new Application())->setDateCreate(
            (new DateTimeImmutable())
                ->add(DateInterval::createFromDateString('-1 day'))
        );
        $this->applicationProcessor->process(
            $application,
            $this->createMock(Operation::class),
        );

        $this->assertNotNull($this->applicationProcessor->getApplication()->getDateUpdate());
        $this->assertNotEquals(
            $this->applicationProcessor->getApplication()->getDateCreate(),
            $this->applicationProcessor->getApplication()->getDateUpdate(),
        );
    }

    public function testDataIsNotInstanceOfApplication(): void
    {
        $this->expectException(TypeError::class);

        $this->applicationProcessor->process(
            [],
            $this->createMock(Operation::class),
        );
    }

    public function testSecurityGetUserReturnIsNotInstanceOfUser(): void
    {
        $security = $this->createStub(Security::class);
        $security->method('getUser')->willReturn(null);

        $this->applicationProcessor = new ApplicationProcessor(
            $this->createMock(ProcessorInterface::class),
            $security,
        );

        $this->expectException(TypeError::class);

        $this->applicationProcessor->process(
            $this->createMock(Application::class),
            $this->createMock(Operation::class),
        );
    }
}
