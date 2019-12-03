<?php

namespace App\Repository;

use App\Entity\Categories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Categories|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categories|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categories[]    findAll()
 * @method Categories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categories::class);
    }

    /**
     * remove all use for History flag currently present
     */
    public function removeAllUseForHistory() {
        $db = $this->getEntityManager()->getConnection();

        $sql = "UPDATE categories SET use_for_history = 0";

        $stmt = $db->prepare($sql);
        $stmt->execute();
    }

    public function getAllWithCounter()
    {
        $result = $this->createQueryBuilder('cat')->
                        select('cat.id, cat.name, cat.useForHistory, COUNT(DISTINCT cur.id) + COUNT(DISTINCT r.id) AS useCount')->
                        leftJoin('App\Entity\Currents', 'cur', 'WITH', 'cur.category = cat.id')->
                        leftJoin('App\Entity\Recuring', 'r', 'WITH', 'r.category = cat.id')->
                        orderBy("cat.name", "ASC")->
                        groupBy("cat.id");

        return $result->getQuery()->execute();
    }
}
