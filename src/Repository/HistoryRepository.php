<?php

namespace App\Repository;

use App\Entity\History;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method History|null find($id, $lockMode = null, $lockVersion = null)
 * @method History|null findOneBy(array $criteria, array $orderBy = null)
 * @method History[]    findAll()
 * @method History[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, History::class);
    }

    public function getMonthsList()
    {
        $qb = $this->createQueryBuilder('h')
            ->select(['h.year', 'h.month'])
            ->groupBy('h.year')
            ->addGroupBy('h.month')
            ->orderBy('h.year', 'DESC')
            ->addOrderBy('h.month', 'DESC');

        return $qb->getQuery()->execute();
    }

    public function getYearsList()
    {
        $qb = $this->createQueryBuilder('h')
            ->select(['h.year'])
            ->groupBy('h.year')
            ->orderBy('h.year', 'DESC');

        return $qb->getQuery()->execute();
    }

    public function getChartHistoryData($year)
    {
        $db = $this->getEntityManager()->getConnection();

        $sql = "    SELECT CONCAT(CAST(month AS Char),'/',CAST(year AS CHAR)) AS period,  ROUND(SUM(value),2) AS sum
                    FROM history o  
                    WHERE year = :year
                    GROUP BY year, month               
               UNION
                    SELECT 'current' AS period, ROUND(SUM(value),2) AS sum
                    FROM currents o  
                    WHERE YEAR(date) = :year
                    GROUP BY period";

        $stmt = $db->prepare($sql);
        $stmt->bindParam("year", $year, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // /**
    //  * @return History[] Returns an array of History objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?History
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
