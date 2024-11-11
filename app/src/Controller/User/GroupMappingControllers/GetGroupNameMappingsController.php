<?php

namespace App\Controller\User\GroupMappingControllers;

use App\Builders\User\GroupNameMapping\GroupNameMappingResponseBuilder;
use App\Entity\User\User;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Services\User\GroupMappingServices\GetGroupNameMappingHandler;
use App\Traits\HomeAppAPITrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'group-mapping')]
class GetGroupNameMappingsController extends AbstractController
{
    use HomeAppAPITrait;

    private RequestQueryParameterHandler $requestQueryParameterHandler;

    public function __construct(RequestQueryParameterHandler $requestQueryParameterHandler)
    {
        $this->requestQueryParameterHandler = $requestQueryParameterHandler;
    }

    #[Route('', name: 'get-all-group-name-mappings', methods: [Request::METHOD_GET])]
    public function getGroupNameMappings(Request $request, GetGroupNameMappingHandler $groupNameMappingHandler): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $request->get(RequestQueryParameterHandler::RESPONSE_TYPE, RequestTypeEnum::FULL->value),
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

        $groupNameMappingsForUser = $groupNameMappingHandler->getGroupNameMappingsForUser($user);
        if (empty($groupNameMappingsForUser)) {
            return $this->sendSuccessfulJsonResponse();
        }

        $groupNameMappingResponseDTOs = [];
        foreach ($groupNameMappingsForUser as $groupNameMapping) {
            $groupNameMappingResponseDTOs[] = GroupNameMappingResponseBuilder::buildGroupNameFullResponseDTO($groupNameMapping);
        }

        try {
            $normalizedGroupNameMappingResponseDTOs = $this->normalize($groupNameMappingResponseDTOs, [$requestDTO->getResponseType()]);
        } catch (NotEncodableValueException) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedGroupNameMappingResponseDTOs);
    }
}
