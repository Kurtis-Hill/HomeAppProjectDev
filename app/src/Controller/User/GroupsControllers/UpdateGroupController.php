<?php

namespace App\Controller\User\GroupsControllers;

use App\Builders\User\GroupName\GroupResponseDTOBuilder;
use App\Builders\User\GroupName\UpdateGroupDTOBuilder;
use App\DTOs\User\Request\GroupDTOs\UpdateGroupRequestDTO;
use App\Entity\User\Group;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Exceptions\User\GroupExceptions\GroupValidationException;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Services\User\GroupServices\UpdateGroupHandler;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\GroupVoter;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-groups/')]
class UpdateGroupController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    private LoggerInterface $logger;

    private RequestQueryParameterHandler $requestQueryParameterHandler;

    public function __construct(LoggerInterface $elasticLogger, RequestQueryParameterHandler $requestQueryParameterHandler)
    {
        $this->logger = $elasticLogger;
        $this->requestQueryParameterHandler = $requestQueryParameterHandler;
    }

    #[Route('{groupID}/update', name: 'update-group', methods: [Request::METHOD_PATCH, Request::METHOD_PUT])]
    public function updateGroupName(
        Group $groupID,
        Request $request,
        ValidatorInterface $validator,
        UpdateGroupHandler $updateGroupHandler,
    ): JsonResponse {
        $updateGroupRequestDTO = new UpdateGroupRequestDTO();
        try {
            $this->deserializeRequest(
                $request->getContent(),
                UpdateGroupRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $updateGroupRequestDTO],
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

        $validationErrors = $validator->validate($updateGroupRequestDTO);
        if ($this->checkIfErrorsArePresent($validationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($validationErrors));
        }

        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $request->get(RequestQueryParameterHandler::RESPONSE_TYPE, RequestTypeEnum::FULL->value),
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

        $updateGroupDTO = UpdateGroupDTOBuilder::buildUpdateGroupDTO(
            $updateGroupRequestDTO->getGroupName(),
            $groupID
        );

        try {
            $this->denyAccessUnlessGranted(GroupVoter::UPDATE_GROUP, $updateGroupDTO);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        try {
            $updateGroupHandler->updateGroup($updateGroupDTO);
        } catch (GroupValidationException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidationErrors());
        } catch (OptimisticLockException|ORMException) {
            return $this->sendInternalServerErrorJsonResponse();
        }

        $groupResponseDTO = GroupResponseDTOBuilder::buildGroupNameResponseDTO($groupID);
        try {
            $normalizedGroupResponseDTO = $this->normalize($groupResponseDTO, [$requestDTO->getResponseType()]);
        } catch (NotEncodableValueException) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE . ' Group Saved']);
        }

        return $this->sendSuccessfulJsonResponse($normalizedGroupResponseDTO);
    }
}
