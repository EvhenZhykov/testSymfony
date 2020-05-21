<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class ReportRepository extends EntityRepository
{
    /**
     * @return mixed
     */
    public function getAllFileNames()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT r.id, r.fileName FROM App:Report r ORDER BY r.id DESC'
            )
            ->getResult();
    }
}