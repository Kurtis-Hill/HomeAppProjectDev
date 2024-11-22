<?php

namespace App\Controller\User\GroupsControllers;

use App\Builders\User\GroupName\GroupResponseDTOBuilder;
use App\Entity\User\Group;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestTypeEnum;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\GroupVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-groups')]
class GetSingleGroupController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[Route('/{group}', name: 'get-single-user-group', methods: [Request::METHOD_GET])]
    public function getSingleGroup(Group $group): JsonResponse
    {
        $allowedToViewGroup = $this->isGranted(GroupVoter::GET_SINGLE_GROUP, $group);
        if ($allowedToViewGroup !== true) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::FORBIDDEN_ACTION]);
        }

        $groupResponseDTO = GroupResponseDTOBuilder::buildGroupNameResponseDTO($group);
        try {
            $normalizedResponse = $this->normalize($groupResponseDTO, [RequestTypeEnum::FULL->value]);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
