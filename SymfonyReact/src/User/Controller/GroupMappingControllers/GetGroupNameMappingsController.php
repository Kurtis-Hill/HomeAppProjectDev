<?php

namespace App\User\Controller\GroupMappingControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Exceptions\ValidatorProcessorException;
use App\Common\Services\RequestQueryParameterHandler;
use App\Common\Services\RequestTypeEnum;
use App\User\Builders\GroupNameMapping\GroupNameMappingResponseBuilder;
use App\User\Entity\User;
use App\User\Services\GroupMappingServices\GetGroupNameMappingHandler;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'group-mapping/')]
class GetGroupNameMappingsController extends AbstractController
{
    use HomeAppAPITrait;

    private RequestQueryParameterHandler $requestQueryParameterHandler;

    public function __construct(RequestQueryParameterHandler $requestQueryParameterHandler)
    {
        $this->requestQueryParameterHandler = $requestQueryParameterHandler;
    }

    #[Route('all', name: 'get-all-group-name-mappings', methods: [Request::METHOD_GET])]
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
            $normalizedGroupNameMappingResponseDTOs = $this->normalizeResponse($groupNameMappingResponseDTOs, [$requestDTO->getResponseType()]);
        } catch (NotEncodableValueException) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedGroupNameMappingResponseDTOs);
    }
}
