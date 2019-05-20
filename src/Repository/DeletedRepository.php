<?php

namespace App\Repository;

use App\Entity\Deleted;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Deleted|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deleted|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deleted[]    findAll()
 * @method Deleted[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeletedRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Deleted::class);
    }

    // /**
    //  * @return Deleted[] Returns an array of Deleted objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Deleted
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
