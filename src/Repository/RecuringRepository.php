<?php

namespace App\Repository;

use App\Entity\Recuring;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Recuring|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recuring|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recuring[]    findAll()
 * @method Recuring[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecuringRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Recuring::class);
    }

    // /**
    //  * @return Recuring[] Returns an array of Recuring objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Recuring
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
