<?php

namespace App\Repository;

use App\Entity\Joke;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Joke|null find($id, $lockMode = null, $lockVersion = null)
 * @method Joke|null findOneBy(array $criteria, array $orderBy = null)
 * @method Joke[]    findAll()
 * @method Joke[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JokeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Joke::class);
    }

    /**
     * @param string $term
     * @return QueryBuilder
     */
    public function searchQuery(string $term): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.joke LIKE :term')
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