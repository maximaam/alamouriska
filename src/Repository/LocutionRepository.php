<?php

namespace App\Repository;

use App\Entity\Locution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Locution|null find($id, $lockMode = null, $lockVersion = null)
 * @method Locution|null findOneBy(array $criteria, array $orderBy = null)
 * @method Locution[]    findAll()
 * @method Locution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocutionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Locution::class);
    }

    // /**
    //  * @return Locution[] Returns an array of Locution objects
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
    public function findOneBySomeField($value): ?Locution
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
