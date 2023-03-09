<?php

namespace App\User\Controller\GroupNameMappingControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\User\Builders\GroupNameMapping\GroupNameMappingResponseBuilder;
use App\User\Entity\User;
use App\User\Services\GroupMappingServices\GetGroupNameMappingHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'group-mapping/')]
class GetGroupNameMappingsController extends AbstractController
{
    use HomeAppAPITrait;

    #[Route('all', name: 'get-all-group-name-mappings', methods: [Request::METHOD_GET])]
    public function getGroupNameMappings(GetGroupNameMappingHandler $groupNameMappingHandler): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        $groupNameMappingsForUser = $groupNameMappingHandler->getGroupNameMappingsForUser($user);
        if (empty($groupNameMappingsForUser)) {
            return $this->sendSuccessfulJsonResponse();
        }

        $groupNameMappingResponseDTOs = [];
        foreach ($groupNameMappingsForUser as $groupNameMapping) {
            $groupNameMappingResponseDTOs[] = GroupNameMappingResponseBuilder::buildGroupNameResponseDTO($groupNameMapping);
        }

        try {
            $normalizedGroupNameMappingResponseDTOs = $this->normalizeResponse($groupNameMappingResponseDTOs);
        } catch (NotEncodableValueException) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedGroupNameMappingResponseDTOs);
    }
}
