<?php

namespace App\User\Controller\GroupsControllers;

use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\User\Builders\GroupName\GroupNameResponseDTOBuilder;
use App\User\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-groups/')]
class GetGroupsController extends AbstractController
{
    use HomeAppAPITrait;

    #[Route('all', name: 'get-user-groups')]
    public function getUsersGroups(Security $token): Response
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return $this->sendBadRequestJsonResponse();
        }

        $groupNameDTOs = [];
        foreach ($user->getUserGroupMappingEntities() as $groupName) {
            $groupNameDTOs[] = GroupNameResponseDTOBuilder::buildGroupNameResponseDTO(
                $groupName->getGroupName()
            );
        }

        try {
            $normalizedGroupNames = $this->normalizeResponse($groupNameDTOs);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse(['something went wrong preparing the data']);
        }

        return $this->sendSuccessfulJsonResponse($normalizedGroupNames);
    }
}
