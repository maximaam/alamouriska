<?php

namespace App\Repository;

use App\Entity\Liking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Liking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Liking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Liking[]    findAll()
 * @method Liking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Liking::class);
    }
}
