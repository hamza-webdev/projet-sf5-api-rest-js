<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api", )
 */
class ProjectApiController extends AbstractController
{
    /**
 * @Route("/users", name="users", methods={"GET"})
 */
    public function liste(UserRepository $userRepo)
    {
        // On récupère la liste des articles
        $users = $userRepo->findAll();

        // On spécifie qu'on utilise l'encodeur JSON
        $encoders = [new JsonEncoder()];

        // On instancie le "normaliseur" pour convertir la collection en tableau
        $normalizers = [new ObjectNormalizer()];

        // On instancie le convertisseur
        $serializer = new Serializer($normalizers, $encoders);

        // On convertit en json
        $jsonContent = $serializer->serialize($users, 'json', [
        'circular_reference_handler' => function ($object) {
            return $object->getId();
        }
    ]);

        // On instancie la réponse
        $response = new Response($jsonContent);

        // On ajoute l'entête HTTP
        $response->headers->set('Content-Type', 'application/json');

        // On envoie la réponse
        return $response;
    }

    /**
 * @Route("/users/{id}", name="user", methods={"GET"})
 */
    public function getUserById(User $user)
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($user, 'json', [
        'circular_reference_handler' => function ($object) {
            return $object->getId();
        }
    ]);
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
 * @Route("/users/add", name="ajout", methods={"POST"})
 */
    public function addArticle(Request $request)
    {
        // On vérifie si la requête est une requête Ajax
        if ($request->isXmlHttpRequest()) {
            // On instancie un nouvel article
            $user = new User();

            // On décode les données envoyées
            $donnees = json_decode($request->getContent());

            // On hydrate l'objet
            $user->setEmail($donnees->email);
            $user->setPassword($donnees->password);
            //$user->setPassword($this->userPasswordEncoderInterface->encodePassword($user, 'badpassword'))

            // On sauvegarde en base
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // On retourne la confirmation
            return new Response('ok', 201);
        }
        return new Response('Failed', 404);
    }

    /**
     * @Route("/users/editer/{id}", name="edit", methods={"PUT"})
     */
    public function editUser(?User $user, Request $request)
    {
        // On vérifie si la requête est une requête Ajax
        if ($request->isXmlHttpRequest()) {

        // On décode les données envoyées
            $donnees = json_decode($request->getContent());

            // On initialise le code de réponse
            $code = 200;

            // Si l'article n'est pas trouvé
            if (!$user) {
                // On instancie un nouvel article
                $user = new User();
                // On change le code de réponse
                $code = 201;
            }

            // On hydrate l'objet
            $user->setEmail($donnees->email);
            $user->setPassword($donnees->password);


            // On sauvegarde en base
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // On retourne la confirmation
            return new Response('ok', $code);
        }
        return new Response('Failed', 404);
    }

    /**
 * @Route("/users/supprimer/{id}", name="supprime", methods={"DELETE"})
 */
    public function removeArticle(User $user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();
        return new Response('ok');
    }

    /**
     * @Route("/project", name="project_api")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ProjectApiController.php',
        ]);
    }
}
