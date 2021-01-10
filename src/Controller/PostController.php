<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    /**
     * @Route("/api/post", name="api_post_get", methods={"GET"})
     */
     
    public function index(PostRepository $postRepository, SerializerInterface $serializers): JsonResponse
    {
        var_dump($postRepository->findAll());
        die();
        return new JsonResponse(
                $serializers->serialize($postRepository->findAll(), 'json'),
                JsonResponse::HTTP_OK,
                [],
                true
        );

    }
}
