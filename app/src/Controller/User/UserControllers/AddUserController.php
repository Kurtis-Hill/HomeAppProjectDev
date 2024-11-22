<?php

namespace App\Controller\User\UserControllers;

use App\Builders\User\User\UserResponseBuilder;
use App\DTOs\User\Request\UserDTOs\NewUserRequestDTO;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Exceptions\User\GroupExceptions\GroupMappingValidationException;
use App\Exceptions\User\GroupExceptions\GroupNotFoundException;
use App\Exceptions\User\GroupExceptions\GroupValidationException;
use App\Exceptions\User\UserExceptions\UserCreationValidationErrorsException;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Services\User\User\UserCreationHandler;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\UserVoter;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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

#[Route(CommonURL::USER_HOMEAPP_API_URL)]
class AddUserController extends AbstractController
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

    #[Route('', name: 'add_user', methods: [Request::METHOD_POST])]
    public function addNewUser(
        Request $request,
        ValidatorInterface $validator,
        UserCreationHandler $userCreationHandler,
    ): JsonResponse {
        $newUserRequestDTO = new NewUserRequestDTO();

        try {
            $this->deserializeRequest(
                $request->getContent(),
                NewUserRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $newUserRequestDTO]
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

        $validationErrors = $validator->validate($newUserRequestDTO);
        if ($this->checkIfErrorsArePresent($validationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($validationErrors));
        }

        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $request->get(RequestQueryParameterHandler::RESPONSE_TYPE, RequestTypeEnum::SENSITIVE_FULL->value),
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

        try {
            $this->denyAccessUnlessGranted(UserVoter::ADD_NEW_USER);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        try {
            $newUser = $userCreationHandler->handleNewUserCreation(
                $newUserRequestDTO->getFirstName(),
                $newUserRequestDTO->getLastName(),
                $newUserRequestDTO->getEmail(),
                $newUserRequestDTO->getGroupName(),
                $newUserRequestDTO->getPassword(),
                null,
                $newUserRequestDTO->getRoles(),
                true,
            );
        } catch (UserCreationValidationErrorsException|GroupValidationException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidationErrors());
        } catch (GroupMappingValidationException|GroupNotFoundException|OptimisticLockException|UniqueConstraintViolationException|ORMException $e) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN]);
        }

        try {
            $userCreationHandler->saveUser($newUser);
            $this->logger->info('New user created', ['user' => $newUser, 'createdBy' => $this->getUser()?->getUserIdentifier()]);
        } catch (ORMException|OptimisticLockException) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN]);
        }

        $userResponseDTO = UserResponseBuilder::buildUserResponseDTO($newUser);
        try {
            $normalizedUserResponseDTO = $this->normalize($userResponseDTO, [$requestDTO->getResponseType()]);
        } catch (NotEncodableValueException) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE], ['User created']);
        }

        return $this->sendCreatedResourceJsonResponse($normalizedUserResponseDTO);
    }
}
