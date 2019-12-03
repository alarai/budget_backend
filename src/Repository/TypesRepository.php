<?php

namespace App\Repository;

use App\Entity\Types;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Types|null find($id, $lockMode = null, $lockVersion = null)
 * @method Types|null findOneBy(array $criteria, array $orderBy = null)
 * @method Types[]    findAll()
 * @method Types[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Types::class);
    }

    /**
     * remove all use for History flag currently present
     */
    public function removeAllUseForHistory() {
        $db = $this->getEntityManager()->getConnection();

        $sql = "UPDATE types SET use_for_history = 0";

        $stmt = $db->prepare($sql);
        $stmt->execute();
    }

    public function getAllWithCounter()
    {
        $result = $this->createQueryBuilder('t')->
        select('t.id, t.name, t.useForHistory, COUNT(DISTINCT cur.id) + COUNT( DISTINCT r.id) AS useCount')->
        leftJoin('App\Entity\Currents', 'cur', 'WITH', 'cur.type = t.id')->
        leftJoin('App\Entity\Recuring', 'r', 'WITH', 'r.type = t.id')->
        orderBy("t.name", "ASC")->
        groupBy("t.id");

        return $result->getQuery()->execute();
    }
}
