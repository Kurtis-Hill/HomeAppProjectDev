<?php

namespace App\User\Services\GroupMappingServices;

use App\Authentication\Entity\GroupNameMapping;
use App\Authentication\Repository\ORM\GroupNameMappingRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Log\LoggerInterface;

class DeleteGroupNameMappingHandler
{
    private GroupNameMappingRepository $groupNameMappingRepository;

    private LoggerInterface $elasticLogger;

    public function __construct(GroupNameMappingRepository $groupNameMappingRepository, LoggerInterface $elasticLogger)
    {
        $this->groupNameMappingRepository = $groupNameMappingRepository;
        $this->elasticLogger = $elasticLogger;
    }

    public function deleteGroupNameMapping(GroupNameMapping $groupNameMapping): bool
    {
        try {
            $this->groupNameMappingRepository->remove($groupNameMapping);
            $this->groupNameMappingRepository->flush();
        } catch (ORMException|OptimisticLockException $e) {
            $this->elasticLogger->error('Error deleting group name mapping', [
                'groupNameMappingID' => $groupNameMapping->getGroupNameMappingID(),
                $e->getMessage(),
            ]);

            return false;
        }

        return true;
    }
}
