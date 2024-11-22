<?php

namespace App\Controller\User\UserControllers;

use App\Entity\User\User;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\User\User\DeleteUserHandler;
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

#[Route(CommonURL::USER_HOMEAPP_API_URL)]
class DeleteUserController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    private LoggerInterface $elasticLogger;

    public function __construct(LoggerInterface $elasticLogger)
    {
        $this->elasticLogger = $elasticLogger;
    }

    #[Route('{userID}', name: 'delete_user', methods: [Request::METHOD_DELETE])]
    public function deleteUser(User $user, DeleteUserHandler $deleteUserHandler): JsonResponse
    {
        $this->denyAccessUnlessGranted(UserVoter::DELETE_USER, $user);

        $userIDBeforeDeletion = $user->getUserID();

        try {
            $deleteUserHandler->deleteUser($user);
        } catch (ORMException|OptimisticLockException) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PROCESS_REQUEST]);
        }

        $this->elasticLogger->info(
            'User with ID: ' . $userIDBeforeDeletion . ' has been deleted',
            [
                'userCompletedTask' => $this->getUser()?->getUserIdentifier(),
                'user_id' => $userIDBeforeDeletion,
            ]
        );

        return $this->sendSuccessfulJsonResponse([
            'User removed with ID: ' . $userIDBeforeDeletion,
        ]);
    }
}
