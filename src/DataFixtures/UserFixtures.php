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

    private ObjectManager $manager;
    private UserPasswordEncoderInterface $encoderPassword;
    public function __construct(UserPasswordEncoderInterface $encoderPassword)
    {
        $this->encoderPassword = $encoderPassword;
    }

    public function load(ObjectManager $manager)
    {

        $users = [];

        for ($i=0; $i < 10; $i++) { 

            $user = User::create(
                "mail+{$i}@gmail.com", 
                $this->encoderPassword->encodePassword((new User()), "password"), 
                "Jean {$i}"
            );
            $users[] = $user;

            $manager->persist($user);

            
           
            }
            $userLikes = array_slice($users, 0, 5);
            //foreach($users as $user)
            //{
                
                for ($j=1; $j < 5; $j++) { 
                    $post = Post::create("contenue de article num {$j}", "titre article {$j}", $user);                  

                    foreach($userLikes as $u)
                    {
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



            // $user = new User();
            // $user->setPassword($encoderPassword->encodePassword($user, 'password)'))
            //     ->setEmail(("email+{$i}@gmail.com"))
            //     ->setName("Francois {$i}")
            //     ;

            
        //}

        $manager->flush();
    }
}
