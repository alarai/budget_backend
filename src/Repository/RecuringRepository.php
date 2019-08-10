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

    public function getNotUsedInCurrent()
    {
        $used = $this->createQueryBuilder('c1')->select('c1.id')->join('App\Entity\Currents', 'c2', 'WITH', 'c1.id = c2.recuring');

        $notUsed = $this->createQueryBuilder('c3');
        $notUsed->where($notUsed->expr()->notIn('c3.id', $used->getDQL()));

        return $notUsed->getQuery()->execute();
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
