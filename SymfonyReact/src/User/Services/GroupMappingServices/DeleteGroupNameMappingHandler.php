<?php

namespace App\User\Services\GroupMappingServices;

use App\Authentication\Entity\GroupMapping;
use App\Authentication\Repository\ORM\GroupMappingRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Log\LoggerInterface;

class DeleteGroupNameMappingHandler
{
    private GroupMappingRepository $groupNameMappingRepository;

    private LoggerInterface $elasticLogger;

    public function __construct(GroupMappingRepository $groupNameMappingRepository, LoggerInterface $elasticLogger)
    {
        $this->groupNameMappingRepository = $groupNameMappingRepository;
        $this->elasticLogger = $elasticLogger;
    }

    public function deleteGroupNameMapping(GroupMapping $groupNameMapping): bool
    {
        try {
            $this->groupNameMappingRepository->remove($groupNameMapping);
            $this->groupNameMappingRepository->flush();
        } catch (ORMException|OptimisticLockException $e) {
            $this->elasticLogger->error('Error deleting group name mapping', [
                'groupMappingID' => $groupNameMapping->getGroupMappingID(),
                $e->getMessage(),
            ]);

            return false;
        }

        return true;
    }
}
