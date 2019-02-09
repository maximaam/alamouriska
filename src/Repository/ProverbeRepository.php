<?php

namespace App\Repository;

use App\Entity\Proverbe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Proverbe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Proverbe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Proverbe[]    findAll()
 * @method Proverbe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProverbeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Proverbe::class);
    }

    // /**
    //  * @return Proverbe[] Returns an array of Proverbe objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Proverbe
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
