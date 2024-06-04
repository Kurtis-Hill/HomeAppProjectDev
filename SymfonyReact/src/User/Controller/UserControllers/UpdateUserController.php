<?php

namespace App\User\Controller\UserControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Exceptions\ValidatorProcessorException;
use App\Common\Services\RequestQueryParameterHandler;
use App\Common\Services\RequestTypeEnum;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Sensors\Exceptions\UserNotAllowedException;
use App\User\Builders\User\UserResponseBuilder;
use App\User\Builders\User\UserUpdateDTOBuilder;
use App\User\DTO\Request\UserDTOs\UpdateUserRequestDTO;
use App\User\Entity\User;
use App\User\Exceptions\GroupExceptions\GroupNotFoundException;
use App\User\Exceptions\UserExceptions\CannotUpdateUsersGroupException;
use App\User\Exceptions\UserExceptions\IncorrectUserPasswordException;
use App\User\Exceptions\UserExceptions\NotAllowedToChangeUserRoleException;
use App\User\Services\User\NotAllowedToUpdatePasswordException;
use App\User\Services\User\UpdateUserHandler;
use App\User\Voters\UserVoter;
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

    #[Route('{userToUpdate}/update', name: 'update_user', methods: [Request::METHOD_PUT, Request::METHOD_PATCH])]
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
