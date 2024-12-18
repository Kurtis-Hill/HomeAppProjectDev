<?php

namespace App\Controller\User\GroupMappingControllers;

use App\Builders\User\GroupNameMapping\GroupNameMappingInternalDTOBuilder;
use App\Builders\User\GroupNameMapping\GroupNameMappingResponseBuilder;
use App\DTOs\User\Request\GroupNameMapping\NewGroupMappingRequestDTO;
use App\Entity\User\User;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Exceptions\User\GroupExceptions\GroupMappingValidationException;
use App\Repository\User\ORM\GroupRepositoryInterface;
use App\Repository\User\ORM\UserRepositoryInterface;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Services\User\GroupMappingServices\AddGroupMappingHandler;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\GroupMappingVoter;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'group-mapping')]
class AddGroupMappingController extends AbstractController
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

    #[Route('', name: 'add-group-name-mappings', methods: [Request::METHOD_POST])]
    public function addGroupNameMappings(
        Request $request,
        ValidatorInterface $validator,
        UserRepositoryInterface $userRepository,
        GroupRepositoryInterface $groupNameRepository,
        AddGroupMappingHandler $addGroupNameMappingHandler,
    ): Response {
        $addGroupNameMappingRequestDTO = new NewGroupMappingRequestDTO();
        try {
            $this->deserializeRequest(
                $request->getContent(),
                NewGroupMappingRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $addGroupNameMappingRequestDTO],
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

        $validationErrors = $validator->validate($addGroupNameMappingRequestDTO);
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

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        $errorMessages = [];
        $userThatIsBeingMapped = $userRepository->find($addGroupNameMappingRequestDTO->getUserID());
        if ($userThatIsBeingMapped === null) {
            $errorMessages[] = sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'User');
        }
        $groupNameToBeMappedTo = $groupNameRepository->find($addGroupNameMappingRequestDTO->getGroupID());
        if ($groupNameToBeMappedTo === null) {
            $errorMessages[] = sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Group');
        }

        if (!empty($errorMessages)) {
            return $this->sendBadRequestJsonResponse($errorMessages);
        }

        $groupNameMappingDTO = GroupNameMappingInternalDTOBuilder::buildGroupNameMappingInternalDTO(
            $userThatIsBeingMapped,
            $groupNameToBeMappedTo,
        );

        try {
            $this->denyAccessUnlessGranted(
                GroupMappingVoter::ADD_NEW_GROUP_NAME_MAPPING,
                $groupNameMappingDTO,
            );
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        try {
            $addGroupNameMappingHandler->addNewGroupNameMappingEntry($groupNameMappingDTO);
        } catch (GroupMappingValidationException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidationErrors());
        } catch (ORMException|OptimisticLockException) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN]);
        }

        $groupNameMappingResponseDTO = GroupNameMappingResponseBuilder::buildGroupNameFullResponseDTO($groupNameMappingDTO->getNewGroupMapping());

        try {
            $normalizedResponse = $this->normalize($groupNameMappingResponseDTO, [$requestDTO->getResponseType()]);
        } catch (NotEncodableValueException) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE], ['Group name mapping Saved']);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
