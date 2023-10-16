<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Movie;

#[Route('/api/v1/movie')]
#[OA\Tag(name: 'Movie')]
#[OA\Response(
    response: 400,
    description: 'Bad request'
)]
#[OA\Response(
    response: 401,
    description: 'Unauthorized'
)]
class MovieController extends AbstractController
{
    #[Route('', name: 'api.movie.create', methods: ['POST'])]
    public function create(
        Request                 $request,
        ValidatorInterface      $validator,
        EntityManagerInterface  $manager,
        SerializerInterface     $serializer
    ): JsonResponse
    {
        $movie = $serializer->deserialize($request->getContent(), Movie::class, 'json');

        if ($movie instanceof Movie) {
            $errors = $validator->validate($movie, null, ['movie:create']);
            if ($errors->count() > 0) {
                return new JsonResponse($serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST, [], true);
            }
            $manager->persist($movie);
            $manager->flush();
        }

        return new JsonResponse($serializer->serialize($movie, 'json'), Response::HTTP_CREATED, [], true);
    }

    #[Route('/{movieId<\d+>}', name: 'api.movie.get_id', methods: ['GET'])]
    #[ParamConverter('movie', options: ['id' => 'movieId'])]
    public function get_id(
        Movie               $movie,
        SerializerInterface $serializer
    ): JsonResponse
    {
        return new JsonResponse($serializer->serialize($movie, 'json'), Response::HTTP_OK, [], true);
    }
}