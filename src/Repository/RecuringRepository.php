<?php

namespace App\Repository;

use App\Entity\Recuring;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
/**
 * @method Recuring|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recuring|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recuring[]    findAll()
 * @method Recuring[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecuringRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recuring::class);
    }

    /**
     * Returns the list of not already used recuring operations in current
     *
     * @return mixed
     */
    public function getNotUsedInCurrent()
    {
        $used = $this->createQueryBuilder('c1')->select('c1.id')->join('App\Entity\Currents', 'c2', 'WITH', 'c1.id = c2.recuring');

        $notUsed = $this->createQueryBuilder('c3');
        $notUsed->where($notUsed->expr()->notIn('c3.id', $used->getDQL()));

        return $notUsed->getQuery()->execute();
    }

    public function getAllWithCounter()
    {
        $result = $this->createQueryBuilder('r')->
                    select('r.id, r.name, cat.name AS categoryName, t.name AS typeName, r.value, COUNT(c.id) AS currentsCount')->
                    leftJoin('App\Entity\Currents', 'c', 'WITH', 'r.id = c.recuring')->
                    innerJoin('App\Entity\Categories', 'cat', 'WITH', 'r.category = cat.id')->
                    innerJoin('App\Entity\Types', 't', 'WITH', 'r.type = t.id')->
                    orderBy("r.name","ASC")->
                    groupBy("r.id");

        return $result->getQuery()->execute();
    }

}
