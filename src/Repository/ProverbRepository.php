<?php

namespace App\Repository;

use App\Entity\Proverb;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Proverb|null find($id, $lockMode = null, $lockVersion = null)
 * @method Proverb|null findOneBy(array $criteria, array $orderBy = null)
 * @method Proverb[]    findAll()
 * @method Proverb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProverbRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Proverb::class);
    }

    /**
     * @param string $term
     * @return QueryBuilder
     */
    public function searchQuery(string $term): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.proverbe LIKE :term')
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
