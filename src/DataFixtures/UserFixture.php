<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher){
$this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $user = new User();
        $user->setEmail('admin@gmail.com');

        // Ajout des rôles
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user , 'admin'));
        $manager->persist($user);

        $manager->flush();
    }
}
