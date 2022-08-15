<?php

namespace App\User\Repository\ORM;

use App\User\Entity\GroupNames;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Exception\ORMException;

/**
 * @method GroupNames|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupNames|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupNames[]    findAll()
 * @method GroupNames[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface GroupNameRepositoryInterface
{
    /**
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function findOneById(int $id): ?GroupNames;
}
