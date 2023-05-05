<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Application;
use App\Entity\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    private Application $app;

    protected function setUp(): void
    {
        $this->app = new Application();
    }

    public function testConstructor(): void
    {
        $app = new Application();
        /** @phpstan-ignore-next-line */
        $this->assertInstanceOf(Application::class, $app);
    }

    public function testId(): void
    {
        $this->assertNull($this->app->getId());
    }

    public function testTitle(): void
    {
        $this->assertNull($this->app->getTitle());
        $this->app->setTitle('title');
        $this->assertEquals('title', $this->app->getTitle());
    }

    public function testDescription(): void
    {
        $this->assertNull($this->app->getDescription());
        $this->app->setDescription('description');
        $this->assertEquals('description', $this->app->getDescription());
    }

    public function testOwner(): void
    {
        $this->assertNull($this->app->getOwner());
        $user = new User();
        $this->app->setOwner($user);
        $this->assertEquals($user, $this->app->getOwner());
    }

    public function testDateCreate(): void
    {
        $this->assertNull($this->app->getDateCreate());
        $date = new DateTimeImmutable();
        $this->app->setDateCreate($date);
        $this->assertEquals($date, $this->app->getDateCreate());
    }

    public function testDateUpdate(): void
    {
        $this->assertNull($this->app->getDateUpdate());
        $date = new DateTimeImmutable();
        $this->app->setDateUpdate($date);
        $this->assertEquals($date, $this->app->getDateUpdate());
    }
}
