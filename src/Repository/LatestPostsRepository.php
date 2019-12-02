<?php

namespace App\Repository;

use App\Entity\LatestPosts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LatestPosts|null find($id, $lockMode = null, $lockVersion = null)
 * @method LatestPosts|null findOneBy(array $criteria, array $orderBy = null)
 * @method LatestPosts[]    findAll()
 * @method LatestPosts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LatestPostsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LatestPosts::class);
    }
}
