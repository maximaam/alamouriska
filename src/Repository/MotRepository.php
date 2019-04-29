<?php

namespace App\Repository;

use App\Entity\Mot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Mot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mot[]    findAll()
 * @method Mot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MotRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Mot::class);
    }

    /**
     * @param string $term
     * @return QueryBuilder
     */
    public function searchQuery(string $term): QueryBuilder
    {
        return $this->createQueryBuilder('m')
            ->where('m.inLatin LIKE :term')
            ->orWhere('m.inTamazight LIKE :term')
            ->orWhere('m.inArabic LIKE :term')
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
