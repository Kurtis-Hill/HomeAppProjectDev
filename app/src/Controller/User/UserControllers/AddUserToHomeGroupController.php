<?php

namespace App\Controller\User\UserControllers;

use App\Builders\User\GroupNameMapping\GroupNameMappingInternalDTOBuilder;
use App\Entity\Authentication\GroupMapping;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Exceptions\User\GroupExceptions\GroupMappingValidationException;
use App\Repository\User\ORM\GroupRepositoryInterface;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\User\GroupMappingServices\AddGroupMappingHandler;
use App\Traits\HomeAppAPITrait;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(CommonURL::USER_HOMEAPP_API_URL)]
class AddUserToHomeGroupController extends AbstractController
{
    use HomeAppAPITrait;

    #[
        Route('{user}/home-group', name: 'add-user-to-home-group', methods: [Request::METHOD_POST]),
        IsGranted('ROLE_ADMIN'),
    ]
    public function addUserToHomeGroup(
        User $user,
        GroupRepositoryInterface $groupRepository,
        AddGroupMappingHandler $addGroupMappingHandler,
    ): JsonResponse {

        $homeGroup = $groupRepository->findOneByName(Group::HOME_APP_GROUP_NAME);
        if (!$homeGroup instanceof Group) {
            return $this->sendNotFoundResponse([sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Home group')]);
        }

        if (in_array($homeGroup->getGroupID(), $user->getAssociatedGroupIDs())) {
            return $this->sendBadRequestJsonResponse([GroupMapping::GROUP_NAME_MAPPING_EXISTS]);
        }

        $groupMappingDTO = GroupNameMappingInternalDTOBuilder::buildGroupNameMappingInternalDTO(
            $user,
            $homeGroup,
        );

        try {
            $addGroupMappingHandler->addNewGroupNameMappingEntry($groupMappingDTO);
        } catch (GroupMappingValidationException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidationErrors());
        } catch (ORMException|OptimisticLockException) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN]);
        }

        return $this->sendSuccessfulJsonResponse(['User added to ' . Group::HOME_APP_GROUP_NAME . ' successfully']);
    }
}
