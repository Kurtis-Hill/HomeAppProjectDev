<?php

namespace App\User\Repository\ORM;

use App\User\Entity\GroupNames;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Exception\ORMException;

interface GroupNameRepositoryInterface
{
    /**
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function findOneById(int $id): ?GroupNames;
}
