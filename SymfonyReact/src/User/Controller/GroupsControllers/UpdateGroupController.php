<?php

namespace App\User\Controller\GroupsControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\User\Builders\GroupName\GroupNameResponseDTOBuilder;
use App\User\Builders\GroupName\UpdateGroupDTOBuilder;
use App\User\DTO\RequestDTOs\GroupDTOs\UpdateGroupRequestDTO;
use App\User\Entity\GroupNames;
use App\User\Exceptions\GroupNameExceptions\GroupNameValidationException;
use App\User\Services\GroupNameServices\UpdateGroupHandler;
use App\User\Voters\GroupVoter;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-groups/')]
class UpdateGroupController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[Route('{groupNameID}/update', name: 'update-group', methods: [Request::METHOD_PATCH, Request::METHOD_PUT])]
    public function updateGroupName(
        GroupNames $groupNameID,
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

        $updateGroupDTO = UpdateGroupDTOBuilder::buildUpdateGroupDTO(
            $updateGroupRequestDTO->getGroupName(),
            $groupNameID
        );

        try {
            $this->denyAccessUnlessGranted(GroupVoter::UPDATE_GROUP, $updateGroupDTO);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        try {
            $updateGroupHandler->updateGroup($updateGroupDTO);
        } catch (GroupNameValidationException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidationErrors());
        } catch (OptimisticLockException|ORMException) {
            return $this->sendInternalServerErrorJsonResponse();
        }

        $groupResponseDTO = GroupNameResponseDTOBuilder::buildGroupNameResponseDTO($groupNameID);
        try {
            $normalizedGroupResponseDTO = $this->normalizeResponse($groupResponseDTO);
        } catch (NotEncodableValueException) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE, 'Group Saved']);
        }

        return $this->sendSuccessfulUpdateJsonResponse($normalizedGroupResponseDTO);
    }
}
