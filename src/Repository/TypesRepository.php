<?php

namespace App\Repository;

use App\Entity\Types;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Types|null find($id, $lockMode = null, $lockVersion = null)
 * @method Types|null findOneBy(array $criteria, array $orderBy = null)
 * @method Types[]    findAll()
 * @method Types[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
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
}
