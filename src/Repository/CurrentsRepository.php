<?php

namespace App\Repository;

use App\Entity\Currents;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Currents|null find($id, $lockMode = null, $lockVersion = null)
 * @method Currents|null findOneBy(array $criteria, array $orderBy = null)
 * @method Currents[]    findAll()
 * @method Currents[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrentsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Currents::class);
    }

    // /**
    //  * @return Currents[] Returns an array of Currents objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Currents
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
