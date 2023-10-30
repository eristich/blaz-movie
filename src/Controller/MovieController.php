<?php

namespace App\Controller;

use App\Entity\Person;
use App\Entity\RelActor;
use App\Entity\RelDirector;
use App\Repository\MovieRepository;
use App\Repository\RelActorRepository;
use App\Repository\RelDirectorRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Movie;
use Nelmio\ApiDocBundle\Annotation\Model;

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
    #[OA\RequestBody(
        required: true,
        content: new Model(type: Movie::class, groups: ['movie:create'])
    )]
    #[OA\Response(
        response: 201,
        description: 'Content of created movie',
        content: new Model(type: Movie::class, groups: ['movie:get-one'])
    )]
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
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        allowEmptyValue: false,
        schema: new OA\Schema(
            type: 'integer'
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Content of targeted movie',
        content: new Model(type: Movie::class, groups: ['movie:get-one'])
    )]
    public function get_id(
        #[MapEntity] Movie      $movie,
        SerializerInterface     $serializer
    ): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($movie, 'json', SerializationContext::create()->setGroups(['movie:get-one'])),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('', name: 'api.movie.get_all', methods: ['GET'])]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        required: false,
        allowEmptyValue: true,
        schema: new OA\Schema(
            type: 'integer',
            default: 1
        )
    )]
    #[OA\Parameter(
        name: 'limit',
        in: 'query',
        required: false,
        allowEmptyValue: true,
        schema: new OA\Schema(
            type: 'integer',
            default: 20
        )
    )]
    #[OA\Parameter(
        name: 'order',
        in: 'query',
        required: false,
        allowEmptyValue: true,
        schema: new OA\Schema(
            type: 'string',
            default: 'DESC'
        )
    )]
    #[OA\Parameter(
        name: 'actors',
        in: 'query',
        required: false,
        allowEmptyValue: true,
        schema: new OA\Schema(
            type: 'string',
            example: '1,2,3'
        )
    )]
    #[OA\Parameter(
        name: 'directors',
        in: 'query',
        required: false,
        allowEmptyValue: true,
        schema: new OA\Schema(
            type: 'string',
            example: '1,2,3'
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'List of movie with pagination',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Movie::class, groups: ['movie:get-many']))
        )
    )]
    public function get_all(
        Request             $request,
        SerializerInterface $serializer,
        MovieRepository     $movieRepository
    ): JsonResponse
    {
        // process query parameters
        $limit = $request->query->getInt('limit', 20);
        $limit = (1 <= $limit) && ($limit <= 20) ? $limit : 20;
        $page = $request->query->getInt('page', 1);
        $offset = ($page - 1) * $limit;
        $order = strtoupper($request->query->getString('order', 'DESC'));
        $order = in_array($order, ['DESC', 'ASC']) ? $order : 'DESC';
        $actorIds = explode(',', $request->query->getString('actors', ''));
        $directorIds = explode(',', $request->query->getString('directors', ''));

        $movies = $movieRepository->findByPersons($actorIds, $directorIds, $order)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return new JsonResponse(
            $serializer->serialize($movies, 'json', SerializationContext::create()->setGroups(['movie:get-one'])),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{id<\d+>}', name: 'api.movie.update', methods: ['PUT'])]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        allowEmptyValue: false,
        schema: new OA\Schema(
            type: 'integer'
        )
    )]
    #[OA\RequestBody(
        required: false,
        content: new Model(type: Movie::class, groups: ['movie:update'])
    )]
    #[OA\Response(
        response: 200,
        description: 'Content of updated movie',
        content: new Model(type: Movie::class, groups: ['movie:get-one'])
    )]
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
            $errors = $validator->validate($inputMovie, null, ['movie:update']);
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

    #[Route('/{id<\d+>}', name: 'api.movie.remove', methods: ['DELETE'])]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        allowEmptyValue: false,
        schema: new OA\Schema(
            type: 'integer'
        )
    )]
    #[OA\Response(
        response: 204,
        description: 'No content',
        content: null
    )]
    public function remove(
        #[MapEntity] Movie      $movie,
        EntityManagerInterface  $manager,
    ): JsonResponse
    {
        $manager->remove($movie);
        $manager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT, [], false);
    }

    #[Route('/{movie<\d+>}/{_personType}/{person<\d+>}', name: 'api.movie.link_person', requirements: ['_personType' => 'director|actor'], methods: ['POST'])]
    #[OA\Parameter(
        name: 'movie',
        in: 'path',
        required: true,
        allowEmptyValue: false,
        schema: new OA\Schema(
            type: 'integer'
        )
    )]
    #[OA\Parameter(
        name: '_personType',
        in: 'path',
        required: true,
        allowEmptyValue: false,
        schema: new OA\Schema(
            type: 'string',
            enum: ['director', 'actor']
        )
    )]
    #[OA\Parameter(
        name: 'person',
        in: 'path',
        required: true,
        allowEmptyValue: false,
        schema: new OA\Schema(
            type: 'integer'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Success to add person in movie'
    )]
    public function linkPerson(
        string                                              $_personType,
        #[MapEntity(mapping: ['movie' => 'id'])] Movie      $movie,
        #[MapEntity(mapping: ['person' => 'id'])] Person    $person,
        EntityManagerInterface                              $manager,
    ): JsonResponse
    {
        if (strtolower($_personType) === 'director') {
            $director = (new RelDirector())->setMovie($movie)->setPerson($person);
            $manager->persist($director);
        } elseif (strtolower($_personType) === 'actor') {
            $actor = (new RelActor())->setMovie($movie)->setPerson($person);
            $manager->persist($actor);
        } else {
            throw new NotFoundHttpException();
        }
        $manager->flush();

        return new JsonResponse(null, Response::HTTP_CREATED, [], false);
    }

    #[Route('/{movie<\d+>}/{_personType}/{person<\d+>}', name: 'api.movie.unlink_person', requirements: ['_personType' => 'director|actor'], methods: ['DELETE'])]
    #[OA\Parameter(
        name: 'movie',
        in: 'path',
        required: true,
        allowEmptyValue: false,
        schema: new OA\Schema(
            type: 'integer'
        )
    )]
    #[OA\Parameter(
        name: '_personType',
        in: 'path',
        required: true,
        allowEmptyValue: false,
        schema: new OA\Schema(
            type: 'string',
            enum: ['director', 'actor']
        )
    )]
    #[OA\Parameter(
        name: 'person',
        in: 'path',
        required: true,
        allowEmptyValue: false,
        schema: new OA\Schema(
            type: 'integer'
        )
    )]
    #[OA\Response(
        response: 204,
        description: 'Success to unlink person from movie'
    )]
    public function unlinkPerson(
        string                                              $_personType,
        #[MapEntity(mapping: ['movie' => 'id'])] Movie      $movie,
        #[MapEntity(mapping: ['person' => 'id'])] Person    $person,
        EntityManagerInterface                              $manager,
        RelActorRepository                                  $relActorRepository,
        RelDirectorRepository                               $relDirectorRepository
    ): JsonResponse
    {
        if (strtolower($_personType) === 'director') {
            $relDirector = $relDirectorRepository->findOneBy(['movie' => $movie, 'person' => $person]);
            $manager->remove($relDirector);
            $manager->persist($relDirector);
        } elseif (strtolower($_personType) === 'actor') {
            $relActor = $relActorRepository->findOneBy(['movie' => $movie, 'person' => $person]);
            $manager->remove($relActor);
        } else {
            throw new NotFoundHttpException();
        }
        $manager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT, [], false);
    }
}