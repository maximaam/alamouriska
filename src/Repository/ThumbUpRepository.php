<?php

namespace App\Repository;

use App\Entity\ThumbUp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ThumbUp|null find($id, $lockMode = null, $lockVersion = null)
 * @method ThumbUp|null findOneBy(array $criteria, array $orderBy = null)
 * @method ThumbUp[]    findAll()
 * @method ThumbUp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThumbUpRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ThumbUp::class);
    }

    // /**
    //  * @return Like[] Returns an array of Like objects
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
    public function findOneBySomeField($value): ?Like
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
