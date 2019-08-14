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

    public function getRemainingPassed()
    {
        $qb = $this->createQueryBuilder('c')
            ->select("SUM(c.value) AS value")
            ->andWhere('c.checked = 1')
            ->getQuery();

        return $qb->execute()[0];
    }

    public function removeAllPassedOperations()
    {
        return $this->createQueryBuilder('c')->delete()->where('c.checked = 1')->getQuery()->execute();
    }

    public function historizeData($month, $year)
    {
        $db = $this->getEntityManager()->getConnection();

        $sql = "INSERT INTO history(month, year, name, date, category_name, category_id, type_name, type_id, was_recuring, value)
                    SELECT ?, ?, cur.name, cur.date, cat.name, cur.category_id, t.name, cur.type_id, IF(cur.recuring_id IS NULL, 0, 1), cur.value 
                    FROM currents cur
                    LEFT JOIN categories cat ON cur.category_id = cat.id
                    LEFT JOIN types t ON cur.type_id = t.id
                    WHERE checked = 1";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $month, \PDO::PARAM_INT);
        $stmt->bindParam(2, $year, \PDO::PARAM_INT);
        $stmt->execute();
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
