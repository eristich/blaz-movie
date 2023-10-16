<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
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

    #[Route('/{id<\d+>}', name: 'api.movie.get_id', methods: ['GET'])]
    public function get_id(
        #[MapEntity] Movie  $movie,
        SerializerInterface $serializer
    ): JsonResponse
    {
        return new JsonResponse($serializer->serialize($movie, 'json'), Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'api.movie.get_all', methods: ['GET'])]
    public function get_all(
        Request             $request,
        SerializerInterface $serializer,
        MovieRepository     $movieRepository
    ): JsonResponse
    {
        $limit = $request->query->getInt('limit', 20);
        $limit = (1 <= $limit) && ($limit <= 20) ? $limit : 20;
        $page = $request->query->getInt('page', 1);
        $offset = ($page - 1) * $limit;
        $order = strtolower($request->query->getString('order', 'DESC'));
        $order = in_array($order, ['desc', 'asc']) ? $order : 'DESC';
        $movies = $movieRepository->findBy([], ['publication_on' => $order], $limit, $offset);
        return new JsonResponse($serializer->serialize($movies, 'json'), Response::HTTP_OK, [], true);
    }

    #[Route('/{id<\d+>}', name: 'api.movie.update', methods: ['PUT'])]
    public function update(
        #[MapEntity] Movie      $movie,
        Request                 $request,
        SerializerInterface     $serializer,
        EntityManagerInterface  $manager,
        ValidatorInterface      $validator
    ): JsonResponse
    {
        $inputMovie = $serializer->deserialize($request->getContent(), Movie::class, 'json');

        if ($inputMovie instanceof Movie) {
            $errors = $validator->validate($movie, null, ['movie:update']);
            if ($errors->count() > 0) {
                return new JsonResponse($serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST, [], true);
            }
            $movie->setName($inputMovie->getName() ?? $movie->getName());
            $movie->setDescription($inputMovie->getDescription() ?? $movie->getDescription());
            $movie->setPublicationOn($inputMovie->getPublicationOn() ?? $movie->getPublicationOn());
            $manager->persist($movie);
            $manager->flush();
        }

        return new JsonResponse($serializer->serialize($movie, 'json'), Response::HTTP_OK, [], true);
    }
}