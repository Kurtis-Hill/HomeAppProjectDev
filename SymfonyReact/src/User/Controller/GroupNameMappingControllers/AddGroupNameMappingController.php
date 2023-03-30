<?php

namespace App\User\Controller\GroupNameMappingControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\User\Builders\GroupNameMapping\GroupNameMappingInternalDTO;
use App\User\Builders\GroupNameMapping\GroupNameMappingResponseBuilder;
use App\User\DTO\RequestDTOs\GroupNameMapping\NewGroupNameMappingRequestDTO;
use App\User\Entity\User;
use App\User\Exceptions\GroupNameExceptions\GroupNameMappingValidationException;
use App\User\Repository\ORM\GroupNameRepositoryInterface;
use App\User\Repository\ORM\UserRepositoryInterface;
use App\User\Services\GroupMappingServices\AddGroupNameMappingHandler;
use App\User\Voters\GroupNameMappingVoter;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'group-mapping/')]
class AddGroupNameMappingController extends AbstractController
{
    use HomeAppAPITrait;

    use ValidatorProcessorTrait;

    #[Route('add', name: 'add-group-name-mappings', methods: [Request::METHOD_POST])]
    public function addGroupNameMappings(
        Request $request,
        ValidatorInterface $validator,
        UserRepositoryInterface $userRepository,
        GroupNameRepositoryInterface $groupNameRepository,
        AddGroupNameMappingHandler $addGroupNameMappingHandler,
    ): Response {
        $addGroupNameMappingRequestDTO = new NewGroupNameMappingRequestDTO();

        try {
            $this->deserializeRequest(
                $request->getContent(),
                NewGroupNameMappingRequestDTO::class,
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

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        $errorMessages = [];
        $userThatIsBeingMapped = $userRepository->find($addGroupNameMappingRequestDTO->getUserID());
        if ($userThatIsBeingMapped === null) {
            $errorMessages[] = sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'User');
        }
        $groupNameToBeMappedTo = $groupNameRepository->find($addGroupNameMappingRequestDTO->getGroupNameID());
        if ($groupNameToBeMappedTo === null) {
            $errorMessages[] = sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'GroupName');
        }

        if (!empty($errorMessages)) {
            return $this->sendBadRequestJsonResponse($errorMessages);
        }

        $groupNameMappingDTO = GroupNameMappingInternalDTO::buildGroupNameMappingInternalDTO(
            $userThatIsBeingMapped,
            $groupNameToBeMappedTo,
        );

        try {
            $this->denyAccessUnlessGranted(
                GroupNameMappingVoter::ADD_NEW_GROUP_NAME_MAPPING,
                $groupNameMappingDTO,
            );
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        try {
            $addGroupNameMappingHandler->addNewGroupNameMappingEntry($groupNameMappingDTO);
        } catch (GroupNameMappingValidationException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidationErrors());
        } catch (ORMException|OptimisticLockException) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN]);
        }

        $groupNameMappingResponseDTO = GroupNameMappingResponseBuilder::buildGroupNameFullResponseDTO($groupNameMappingDTO->getNewGroupNameMapping());

        try {
            $normalizedResponse = $this->normalizeResponse($groupNameMappingResponseDTO);
        } catch (NotEncodableValueException) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE], ['Group name mapping Saved']);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
