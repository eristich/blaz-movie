<?php

namespace App\Repository;

use App\Entity\RelActor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RelActor>
 *
 * @method RelActor|null find($id, $lockMode = null, $lockVersion = null)
 * @method RelActor|null findOneBy(array $criteria, array $orderBy = null)
 * @method RelActor[]    findAll()
 * @method RelActor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelActorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RelActor::class);
    }

//    /**
//     * @return RelActor[] Returns an array of RelActor objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RelActor
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
