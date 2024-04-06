<?php

namespace App\User\Controller\GroupsControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Exceptions\ValidatorProcessorException;
use App\Common\Services\RequestQueryParameterHandler;
use App\Common\Services\RequestTypeEnum;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\User\Builders\GroupName\AddNewGroupNameDTOBuilder;
use App\User\Builders\GroupName\GroupResponseDTOBuilder;
use App\User\DTO\Request\GroupDTOs\NewGroupRequestDTO;
use App\User\Entity\User;
use App\User\Exceptions\GroupExceptions\GroupValidationException;
use App\User\Services\GroupServices\AddGroupHandler;
use App\User\Voters\GroupVoter;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
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
class AddGroupController extends AbstractController
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

    #[Route('add', name: 'add-group', methods: [Request::METHOD_POST])]
    public function addNewGroupName(
        Request $request,
        ValidatorInterface $validator,
        AddGroupHandler $addGroupNameHandler,
    ): JsonResponse {
        $newGroupRequestDTO = new NewGroupRequestDTO();
        try {
            $this->deserializeRequest(
                $request->getContent(),
                NewGroupRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $newGroupRequestDTO],
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
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

        $validationErrors = $validator->validate($newGroupRequestDTO);
        if ($this->checkIfErrorsArePresent($validationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($validationErrors));
        }

        $newGroupDTO = AddNewGroupNameDTOBuilder::buildAddNewGroupDTO($newGroupRequestDTO->getGroupName());
        try {
            $this->denyAccessUnlessGranted(GroupVoter::ADD_NEW_GROUP, $newGroupDTO);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        try {
           $newGroup = $addGroupNameHandler->addNewGroup($newGroupDTO->getGroupName(), $user);
        }  catch (NonUniqueResultException){
            return $this->sendInternalServerErrorJsonResponse();
        } catch (GroupValidationException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidationErrors());
        } catch (ORMException|OptimisticLockException) {
            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::OBJECT_ALREADY_EXISTS, $newGroupDTO->getGroupName())]);
        }

        $groupResponseDTO = GroupResponseDTOBuilder::buildGroupNameResponseDTO($newGroup);
        try {
            $normalizedGroupResponseDTO = $this->normalize($groupResponseDTO, [$requestDTO->getResponseType()]);
        } catch (NotEncodableValueException) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE], ['Group Saved']);
        }

        return $this->sendCreatedResourceJsonResponse($normalizedGroupResponseDTO);
    }
}
