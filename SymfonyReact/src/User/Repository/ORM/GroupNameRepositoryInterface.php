<?php

namespace App\User\Repository\ORM;

use App\Entity\Core\GroupNames;

interface GroupNameRepositoryInterface
{
    public function findOneById(int $id): ?GroupNames;
}
