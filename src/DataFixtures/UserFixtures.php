<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private ObjectManager $_manager;
    private UserPasswordEncoderInterface $_encoderPassword;
    public function __construct(UserPasswordEncoderInterface $encoderPassword)
    {
        $this->_encoderPassword = $encoderPassword;
    }


    public function load(ObjectManager $manager): void
    {
        $users = [];

        for ($i=0; $i < 10; $i++) {
            $user = User::create(
                "mail+{$i}@gmail.com",
                $this->_encoderPassword->encodePassword((new User()), "password"),
                "Jean {$i}"
            );
            $users[] = $user;


            $manager->
            $manager->persist($user);
        }

        $userLikes = array_slice($users, 0, 5);

        for ($j=1; $j < 5; $j++) {
            $post = Post::create("text article {$j}", "titre article {$j}", $user);
            foreach ($userLikes as $u) {
                $post->likeBy($u);
            }

            $manager->persist($post);

            for ($k=0; $k < 5; $k++) {
                $comment = Comment::create(
                    "Le message comment {$k}",
                    $post,
                    $users[array_rand($users)]
                );
                $manager->persist($comment);
            }
        }
        $manager->flush();
    }
}
