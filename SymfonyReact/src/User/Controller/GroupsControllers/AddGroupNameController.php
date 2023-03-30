<?php

namespace App\User\Controller\GroupsControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\User\Builders\GroupName\AddNewGroupNameDTOBuilder;
use App\User\Builders\GroupName\GroupNameResponseDTOBuilder;
use App\User\DTO\RequestDTOs\GroupDTOs\NewGroupRequestDTO;
use App\User\Entity\User;
use App\User\Exceptions\GroupNameExceptions\GroupNameValidationException;
use App\User\Services\GroupNameServices\AddGroupNameHandler;
use App\User\Voters\GroupVoter;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
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
class AddGroupNameController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[Route('add', name: 'add-group', methods: [Request::METHOD_POST])]
    public function addNewGroupName(
        Request $request,
        ValidatorInterface $validator,
        AddGroupNameHandler $addGroupNameHandler,
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
        } catch (GroupNameValidationException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidationErrors());
        } catch (ORMException|OptimisticLockException) {
            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::OBJECT_ALREADY_EXISTS, $newGroupDTO->getGroupName())]);
        }

        $groupResponseDTO = GroupNameResponseDTOBuilder::buildGroupNameResponseDTO($newGroup);
        try {
            $normalizedGroupResponseDTO = $this->normalizeResponse($groupResponseDTO);
        } catch (NotEncodableValueException) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE], ['Group Saved']);
        }

        return $this->sendCreatedResourceJsonResponse($normalizedGroupResponseDTO);
    }
}
