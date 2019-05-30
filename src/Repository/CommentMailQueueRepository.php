<?php

namespace App\Repository;

use App\Entity\CommentMailQueue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CommentMailQueue|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentMailQueue|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentMailQueue[]    findAll()
 * @method CommentMailQueue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentMailQueueRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CommentMailQueue::class);
    }

    // /**
    //  * @return CommentMailQueue[] Returns an array of CommentMailQueue objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CommentMailQueue
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
