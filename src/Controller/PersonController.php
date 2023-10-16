<?php

namespace App\Controller;

use App\Entity\Person;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

#[Route('/api/v1/person')]
#[OA\Tag(name: 'Person')]
#[OA\Response(
    response: 400,
    description: 'Bad request'
)]
#[OA\Response(
    response: 401,
    description: 'Unauthorized'
)]
class PersonController extends AbstractController
{
    #[Route('', name: 'api.person.create', methods: ['POST'])]
    public function create(
        Request                 $request,
        ValidatorInterface      $validator,
        EntityManagerInterface  $manager,
        SerializerInterface     $serializer
    ): Response
    {
        $person = $serializer->deserialize($request->getContent(), Person::class, 'json');

        if ($person instanceof Person) {
            $errors = $validator->validate($person, null, ['person:create']);
            if ($errors->count() > 0) {
                return new JsonResponse($serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST, [], true);
            }
            $manager->persist($person);
            $manager->flush();
        }

        return new JsonResponse($serializer->serialize($person, 'json'), Response::HTTP_CREATED, [], true);
    }

    #[Route('/{id<\d+>}', name: 'api.person.get_id', methods: ['GET'])]
    public function get_id(
        #[MapEntity] Person     $person,
        SerializerInterface     $serializer
    ): JsonResponse
    {
        return new JsonResponse($serializer->serialize($person, 'json'), Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'api.person.get_all', methods: ['GET'])]
    public function get_all(
        Request             $request,
        SerializerInterface $serializer,
        PersonRepository    $personRepository
    ): JsonResponse
    {
        $limit = $request->query->getInt('limit', 20);
        $limit = (1 <= $limit) && ($limit <= 20) ? $limit : 20;
        $page = $request->query->getInt('page', 1);
        $offset = ($page - 1) * $limit;
        $order = strtolower($request->query->getString('order', 'DESC'));
        $order = in_array($order, ['desc', 'asc']) ? $order : 'DESC';
        $persons = $personRepository->findBy([], ['birthday' => $order], $limit, $offset);
        return new JsonResponse($serializer->serialize($persons, 'json'), Response::HTTP_OK, [], true);
    }
}
