<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    private ObjectManager $manager;
    private UserPasswordEncoderInterface $encoderPassword;
    public function __construct(UserPasswordEncoderInterface $encoderPassword)
    {
        $this->encoderPassword = $encoderPassword;
    }

    public function load(ObjectManager $manager)
    {

        for ($i=0; $i < 10; $i++) { 

            $user = User::create(
                "mail+{$i}@gmail.com", 
                $this->encoderPassword->encodePassword((new User()), "password"), 
                "Jean {$i}"
            );
            $manager->persist($user);

            for ($j=1; $j < 5; $j++) { 
                $post = Post::create("contenue de article num {$j}", "titre article {$j}", $user);

                $manager->persist($post);
            }

            // $user = new User();
            // $user->setPassword($encoderPassword->encodePassword($user, 'password)'))
            //     ->setEmail(("email+{$i}@gmail.com"))
            //     ->setName("Francois {$i}")
            //     ;

            
        }

        $manager->flush();
    }
}
