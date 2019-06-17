<?php

namespace App\Repository;

use App\Entity\LatestPosts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LatestPosts|null find($id, $lockMode = null, $lockVersion = null)
 * @method LatestPosts|null findOneBy(array $criteria, array $orderBy = null)
 * @method LatestPosts[]    findAll()
 * @method LatestPosts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LatestPostsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LatestPosts::class);
    }

    // /**
    //  * @return LatestPosts[] Returns an array of LatestPosts objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LatestPosts
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
