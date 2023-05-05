<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Application;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testConstructor(): void
    {
        $user = new User();
        /** @phpstan-ignore-next-line */
        $this->assertInstanceOf(User::class, $user);
    }

    public function testId(): void
    {
        $this->assertNull($this->user->getId());
    }

    public function testPhone(): void
    {
        $this->assertNull($this->user->getPhone());
        $this->user->setPhone('79998887766');
        $this->assertEquals('79998887766', $this->user->getPhone());
    }

    public function testEmail(): void
    {
        $this->assertNull($this->user->getEmail());
        $this->user->setEmail('test@email.ru');
        $this->assertEquals('test@email.ru', $this->user->getEmail());
    }

    public function testPassword(): void
    {
        $this->assertNull($this->user->getPassword());
        $this->user->setPassword('123456');
        $this->assertEquals('123456', $this->user->getPassword());
    }

    public function testPlainPassword(): void
    {
        $this->assertNull($this->user->getPlainPassword());
        $this->user->setPlainPassword('123456');
        $this->assertEquals('123456', $this->user->getPlainPassword());
    }

    public function testEraseCredentials(): void
    {
        $this->assertNull($this->user->getPlainPassword());
        $this->user->setPlainPassword('123456');
        $this->user->eraseCredentials();
        $this->assertNull($this->user->getPlainPassword());
    }

    public function testRoles(): void
    {
        $this->assertEquals(['ROLE_USER'], $this->user->getRoles());
        $this->user->setRoles(['ROLE_ADMIN']);
        $this->assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $this->user->getRoles());
    }

    public function testUserIdentifier(): void
    {
        $this->assertEmpty($this->user->getUserIdentifier());
        $this->user->setEmail('test@email.ru');
        $this->assertEquals('test@email.ru', $this->user->getUserIdentifier());
    }

    public function testApplications(): void
    {
        $this->assertEquals(new ArrayCollection(), $this->user->getApplications());

        $app1 = new Application();
        $app2 = new Application();
        $collection = new ArrayCollection([$app1, $app2]);

        $this->user->addApplication($app1);
        $this->user->addApplication($app2);

        $this->assertEquals($collection, $this->user->getApplications());
    }

    public function testRemoveApplication(): void
    {
        $app1 = new Application();
        $app2 = new Application();

        $this->user->addApplication($app1);
        $this->user->addApplication($app2);

        $this->user->removeApplication($app1);
        $this->assertNotContains($app1, $this->user->getApplications());

        $this->user->removeApplication($app2);
        $this->assertNotContains($app2, $this->user->getApplications());

        $this->assertEmpty($this->user->getApplications());
    }
}
