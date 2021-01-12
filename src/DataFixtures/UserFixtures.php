<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\DataFixtures\AuthorFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private ObjectManager $manager;
    private \Faker\Generator $faker;
    private UserPasswordEncoderInterface $userPasswordEncoderInterface;
    private TokenGeneratorInterface $tokenGenerator;

    public function __construct(
        UserPasswordEncoderInterface $userPasswordEncoderInterface,
        TokenGeneratorInterface $tokenGenerator
    )
    {
        $this->userPasswordEncoderInterface = $userPasswordEncoderInterface;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker = Factory::create();
        $this->generateUsers(2);
        $this->manager->flush();
    }

    public function getDependencies()
    {
        return [
            AuthorFixtures::class,
        ];
    }

    public function generateUsers(int $number): void
    {
        $isVerified = [true, false];

        for ($i = 0; $i < $number; $i++) {
            $user = new User();
            $user->setPassword($this->userPasswordEncoderInterface->encodePassword($user, 'badpassword')) ->setEmail($this->faker->email)
            ->setIsVerified($isVerified[$i])
            ->setRegisterToken($this->tokenGenerator->generateToken())
            ->setAuthor($this->getReference("author{$i}"))
            ;

            $this->manager->persist($user);
        }
    }
}
