<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    /** @phpstan-ignore-next-line */
    private array $fixtures;

    public function __construct(
        private UserPasswordHasherInterface $hasher,
    ) {
        $this->fixtures = [
            [
                'email' => 'admin@admin.ru',
                'phone' => '79998886655',
                'password' => 'admin',
                'roles' => ['ROLE_ADMIN'],
            ],
            [
                'email' => 'user@user.ru',
                'phone' => '71112223344',
                'password' => 'user',
                'roles' => ['ROLE_USER'],
            ]
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->fixtures as $item) {
            $user = new User();
            $user->setEmail($item['email']);
            $user->setPhone($item['phone']);
            $user->setRoles($item['roles']);

            $password = $this->hasher->hashPassword($user, $item['password']);
            $user->setPassword($password);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
