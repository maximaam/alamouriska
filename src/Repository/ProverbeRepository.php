<?php

namespace App\Repository;

use App\Entity\Proverbe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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
        return $this->searchQuery($term)->getQuery()->getArrayResult();
    }
}
