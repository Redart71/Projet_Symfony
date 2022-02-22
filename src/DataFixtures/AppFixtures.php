<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User($this->passwordHasher);
        $user->setEmail('arthurjfr71@gmail.com')->setPassword('Password');
        $manager->persist($user);

        $manager->flush();
    }

    /**
     * Get the value of passwordHasher
     */ 
    public function getPasswordHasher()
    {
        return $this->passwordHasher;
    }

    /**
     * Set the value of passwordHasher
     *
     * @return  self
     */ 
    public function setPasswordHasher($passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;

        return $this;
    }
}
