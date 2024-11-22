<?php

namespace App\Controller\User\UserControllers;

use App\Builders\User\User\UserResponseBuilder;
use App\Builders\User\User\UserUpdateDTOBuilder;
use App\DTOs\User\Request\UserDTOs\UpdateUserRequestDTO;
use App\Entity\User\User;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Exceptions\Sensor\UserNotAllowedException;
use App\Exceptions\User\GroupExceptions\GroupNotFoundException;
use App\Exceptions\User\UserExceptions\CannotUpdateUsersGroupException;
use App\Exceptions\User\UserExceptions\IncorrectUserPasswordException;
use App\Exceptions\User\UserExceptions\NotAllowedToChangeUserRoleException;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Services\User\User\NotAllowedToUpdatePasswordException;
use App\Services\User\User\UpdateUserHandler;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\UserVoter;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL)]
class UpdateUserController extends AbstractController
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

    #[Route('{userToUpdate}', name: 'update_user', methods: [Request::METHOD_PUT, Request::METHOD_PATCH])]
    public function updateUser(
        User $userToUpdate,
        Request $request,
        ValidatorInterface $validator,
        UpdateUserHandler $updateUserHandler,
    ): JsonResponse {
        $updateUserRequestDTO = new UpdateUserRequestDTO();

        try {
            $this->deserializeRequest(
                $request->getContent(),
                UpdateUserRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $updateUserRequestDTO]
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }
        $validationErrors = $validator->validate($updateUserRequestDTO);
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
            $this->denyAccessUnlessGranted(UserVoter::UPDATE_USER, $userToUpdate);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $userUpdateDTO = UserUpdateDTOBuilder::buildUserUpdateDTO(
            $userToUpdate,
            $updateUserRequestDTO->getFirstName(),
            $updateUserRequestDTO->getLastName(),
            $updateUserRequestDTO->getEmail(),
            $updateUserRequestDTO->getRoles(),
            $updateUserRequestDTO->getNewPassword(),
            $updateUserRequestDTO->getOldPassword(),
            $updateUserRequestDTO->getGroupID(),
        );

        try {
            $validationErrors = $updateUserHandler->handleUserUpdate($userUpdateDTO);
        } catch (IncorrectUserPasswordException|NotAllowedToChangeUserRoleException|CannotUpdateUsersGroupException|NotAllowedToUpdatePasswordException|GroupNotFoundException $e) {
            return $this->sendBadRequestJsonResponse([$e->getMessage()]);
        } catch (UserNotAllowedException $e) {
            return $this->sendForbiddenAccessJsonResponse([$e->getMessage()]);
        }
        if (!empty($validationErrors)) {
            return $this->sendBadRequestJsonResponse($validationErrors, );
        }

        try {
            $updateUserHandler->saveUser($userUpdateDTO->getUserToUpdate());
        } catch (ORMException|OptimisticLockException) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Save User')]);
        }
        $this->logger->info('User update request', [
            'user' => $userToUpdate->getUserID(),
            'updateUserRequestDTO' => $updateUserRequestDTO,
            'requestDTO' => $requestDTO,
        ]);

        $userResponseDTO = UserResponseBuilder::buildUserResponseDTO($userToUpdate);

        try {
            $normalizedUser = $this->normalize($userResponseDTO, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE], ['Updated User']);
        }

        return $this->sendSuccessfulJsonResponse($normalizedUser);
    }
}
