<?php

namespace App\Controller\User\GroupMappingControllers;

use App\Entity\Authentication\GroupMapping;
use App\Services\API\CommonURL;
use App\Services\User\GroupMappingServices\DeleteGroupNameMappingHandler;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\GroupMappingVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'group-mapping/')]
class DeleteGroupNameMappingController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    public const DELETE_GROUP_NAME_MAPPING_SUCCESS = 'Group name mapping with id: %d deleted successfully';

    #[Route('{groupMappingID}/delete', name: 'delete-group', methods: [Request::METHOD_DELETE])]
    public function deleteGroupNameMapping(
        GroupMapping $groupMappingID,
        DeleteGroupNameMappingHandler $deleteGroupNameMappingHandler,
    ): JsonResponse {
        try {
            $this->denyAccessUnlessGranted(GroupMappingVoter::DELETE_GROUP_NAME_MAPPING, $groupMappingID);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        $deletedGroupNameMappingID = $groupMappingID->getGroupMappingID();
        $deviceDeleteSuccess = $deleteGroupNameMappingHandler->deleteGroupNameMapping($groupMappingID);
        if ($deviceDeleteSuccess === false) {
            return $this->sendBadRequestJsonResponse();
        }

        return $this->sendSuccessfulJsonResponse([sprintf(self::DELETE_GROUP_NAME_MAPPING_SUCCESS, $deletedGroupNameMappingID)]);
    }
}
