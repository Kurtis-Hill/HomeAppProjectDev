<?php

namespace App\User\Controller\GroupNameMappingControllers;

use App\Authentication\Entity\GroupNameMapping;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\User\Services\GroupMappingServices\DeleteGroupNameMappingHandler;
use App\User\Voters\GroupNameMappingVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'group-mapping/')]
class DeleteGroupNameMappingController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    public const DELETE_GROUP_NAME_MAPPING_SUCCESS = 'Group name mapping with id: %d deleted successfully';

    #[Route('{groupNameMappingID}/delete', name: 'delete-group', methods: [Request::METHOD_DELETE])]
    public function deleteGroupNameMapping(
        GroupNameMapping $groupNameMappingID,
        DeleteGroupNameMappingHandler $deleteGroupNameMappingHandler,
    ): JsonResponse {
        try {
            $this->denyAccessUnlessGranted(GroupNameMappingVoter::DELETE_GROUP_NAME_MAPPING, $groupNameMappingID);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse();
        }
        $deletedGroupNameMappingID = $groupNameMappingID->getGroupNameMappingID();
        $deviceDeleteSuccess = $deleteGroupNameMappingHandler->deleteGroupNameMapping($groupNameMappingID);

        if ($deviceDeleteSuccess === false) {
            return $this->sendBadRequestJsonResponse();
        }

        return $this->sendSuccessfulJsonResponse([sprintf(self::DELETE_GROUP_NAME_MAPPING_SUCCESS, $deletedGroupNameMappingID)]);
    }
}
