<?php


namespace App\Repository\Core;

use App\User\Entity\GroupNames;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class GroupNameRepository extends EntityRepository
{
    public function findGroupByName($groupName): GroupNames
    {
        $qb = $this->createQueryBuilder('gno');
        $expr = $qb->expr();

        return $qb->select('gno')
            ->from(GroupNames::class, 'groupName')
            ->where(
                $expr->eq(
                    'gno.groupName', ':groupName'
                )
            )
            ->setParameters(['groupName' => $groupName])
            ->getQuery()
            ->getOneOrNullResult();
    }

}
