<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\ORM\Query\Expr;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 *
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    /**
     * @return QueryBuilder Returns an array of Movie objects
     */
    public function findByPersons(array $actorIds = [], array $directorIds = [], string $order = 'DESC'): QueryBuilder
    {
        $qb = $this->createQueryBuilder('m');
        if (!empty($actorIds)) {
            $qb->innerJoin('m.actors', 'a', Expr\Join::WITH, 'm.id = a.movie');
            $qb->andWhere('a.person IN (:actorIds)');
            $qb->setParameter('actorIds', $actorIds);
        }
        if (!empty($directorIds)) {
            $qb->innerJoin('m.directors', 'd', Expr\Join::WITH, 'm.id = d.movie');
            $qb->andWhere('d.person IN (:directorIds)');
            $qb->setParameter('directorIds', $directorIds);
        }
        $qb->orderBy('m.publication_on', $order);
        return $qb;
    }

//    /**
//     * @return Movie[] Returns an array of Movie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Movie
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
