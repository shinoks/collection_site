<?php

namespace App\Repository;

use App\Entity\Zbiorki;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ZbiorkiRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Zbiorki::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('z')
            ->where('z.something = :value')->setParameter('value', $value)
            ->orderBy('z.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
