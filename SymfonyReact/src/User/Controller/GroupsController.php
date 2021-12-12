<?php

namespace App\User\Controller;

use App\Entity\Core\User;
use App\Traits\API\HomeAppAPIResponseTrait;
use App\User\DTO\GroupDTOs\GroupNameDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route('/HomeApp/api/user-groups/')]
class GroupsController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    #[Route('groups', name: 'get-user-groups')]
    public function getUsersGroups(Security $token): Response
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return $this->sendBadRequestJsonResponse();
        }

        $groupNameDTOs = [];
        foreach ($user->getUserGroupMappingEntities() as $groupName) {
            $groupNameDTOs[] = new GroupNameDTO(
                $groupName->getGroupNameID()->getGroupNameID(),
                $groupName->getGroupNameID()->getGroupName()
            );
        }

        $normaliser = [new ObjectNormalizer()];
        $serializer = new Serializer($normaliser);

        try {
            $normalizedGroupNames = $serializer->normalize($groupNameDTOs);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse(['something went wrong preparing the data']);
        }

        return $this->sendSuccessfulJsonResponse($normalizedGroupNames);
    }
}
