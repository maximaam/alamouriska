<?php

namespace App\Repository;

use App\Entity\Expression;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Expression|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expression|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expression[]    findAll()
 * @method Expression[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpressionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Expression::class);
    }

    /**
     * @param string $term
     * @return QueryBuilder
     */
    public function searchQuery(string $term): QueryBuilder
    {
        return $this->createQueryBuilder('l')
            ->where('l.locution LIKE :term')
            ->setParameter('term', '%'.$term.'%')
            ;
    }

    /**
     * @param string $term
     * @return array
     */
    public function search(string $term): array
    {
        return $this->searchQuery($term)->getQuery()->getResult();
    }
}
