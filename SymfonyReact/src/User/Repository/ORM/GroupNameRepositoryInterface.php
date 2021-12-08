<?php

namespace App\User\Repository\ORM;

use App\User\Entity\GroupNames;

interface GroupNameRepositoryInterface
{
    public function findOneById(int $id): ?GroupNames;
}
