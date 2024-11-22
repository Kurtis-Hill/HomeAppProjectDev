<?php

namespace App\Controller\User\GroupsControllers;

use App\Builders\User\GroupName\GroupResponseDTOBuilder;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Services\User\GroupServices\UserGroupsFinder;
use App\Traits\HomeAppAPITrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-groups')]
class GetGroupsController extends AbstractController
{
    use HomeAppAPITrait;

    private const GROUPS_CONTROLLER_LIMIT = 100;

    private LoggerInterface $logger;

    private RequestQueryParameterHandler $requestQueryParameterHandler;

    public function __construct(LoggerInterface $elasticLogger, RequestQueryParameterHandler $requestQueryParameterHandler)
    {
        $this->logger = $elasticLogger;
        $this->requestQueryParameterHandler = $requestQueryParameterHandler;
    }

    #[Route('', name: 'get-user-groups', methods: [Request::METHOD_GET])]
    public function getUsersGroups(Request $request, UserGroupsFinder $userGroupsFinder): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendBadRequestJsonResponse();
        }

        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $request->get(RequestQueryParameterHandler::RESPONSE_TYPE, RequestTypeEnum::FULL->value),
                $request->get('page', 1),
                $request->get('limit', self::GROUPS_CONTROLLER_LIMIT),
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

        $groupNameDTOs = [];
        foreach ($userGroupsFinder->getUsersGroups($user) as $groupName) {
            $groupNameDTOs[] = GroupResponseDTOBuilder::buildGroupNameResponseDTO(
                $groupName
            );
        }

        try {
            $normalizedGroupNames = $this->normalize($groupNameDTOs, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse(['something went wrong preparing the data']);
        }

        return $this->sendSuccessfulJsonResponse($normalizedGroupNames);
    }

    #[Route('/{group}', name: 'get-single-user-group', methods: [Request::METHOD_GET])]
    public function getSingleGroup(Group $group): JsonResponse
    {
        $groupResponseDTO = GroupResponseDTOBuilder::buildGroupNameResponseDTO($group);
        try {
            $normalizedResponse = $this->normalize($groupResponseDTO, [RequestTypeEnum::FULL->value]);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
