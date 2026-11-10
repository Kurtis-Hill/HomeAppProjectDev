<?php

namespace App\Controller\User\UserControllers;

use App\Builders\User\User\UserResponseBuilder;
use App\Entity\User\User;
use App\Repository\User\ORM\UserRepositoryInterface;
use App\Services\API\CommonURL;
use App\Traits\HomeAppAPITrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use App\Services\Request\RequestTypeEnum;

#[Route(CommonURL::USER_HOMEAPP_API_URL)]
class GetAllUsersController extends AbstractController
{
    use HomeAppAPITrait;

    #[
        Route('', name: 'get-all-users', methods: [Request::METHOD_GET]),
        IsGranted('ROLE_ADMIN'),
    ]
    public function getAllUsers(
        UserRepositoryInterface $userRepository,
        UserResponseBuilder $userResponseBuilder,
    ): JsonResponse {

        /** @var User[] $users */
        $users = $userRepository->findAll();

        $responseDTOs = [];
        foreach ($users as $user) {
            $responseDTOs[] = $userResponseBuilder->buildFullUserResponseDTO($user);
        }

        try {
            $normalized = $this->normalize($responseDTOs, [RequestTypeEnum::SENSITIVE_FULL->value]);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse(['Failed to normalize user list']);
        }

        return $this->sendSuccessfulJsonResponse($normalized);
    }
}
