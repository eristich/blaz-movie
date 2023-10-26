<?php

namespace App\Repository;

use App\Entity\RelDirector;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RelDirector>
 *
 * @method RelDirector|null find($id, $lockMode = null, $lockVersion = null)
 * @method RelDirector|null findOneBy(array $criteria, array $orderBy = null)
 * @method RelDirector[]    findAll()
 * @method RelDirector[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelDirectorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RelDirector::class);
    }

//    /**
//     * @return RelDirector[] Returns an array of RelDirector objects
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

//    public function findOneBySomeField($value): ?RelDirector
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
