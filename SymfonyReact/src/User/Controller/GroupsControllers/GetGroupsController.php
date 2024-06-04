<?php

namespace App\User\Controller\GroupsControllers;

use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Exceptions\ValidatorProcessorException;
use App\Common\Services\RequestQueryParameterHandler;
use App\Common\Services\RequestTypeEnum;
use App\User\Builders\GroupName\GroupResponseDTOBuilder;
use App\User\Entity\User;
use App\User\Services\GroupServices\UserGroupsFinder;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-groups/')]
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

    #[Route('all', name: 'get-user-groups', methods: [Request::METHOD_GET])]
    public function getUsersGroups(Request $request, UserGroupsFinder $userGroupsFinder): Response
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
}
