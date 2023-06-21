<?php

namespace App\User\Controller\UserControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\User\Entity\User;
use App\User\Services\User\DeleteUserHandler;
use App\User\Voters\UserVoter;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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

    #[Route('{userID}/delete', name: 'delete_user', methods: [Request::METHOD_DELETE])]
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
