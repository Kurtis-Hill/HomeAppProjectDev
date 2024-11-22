<?php

namespace App\Controller\Sensor\TriggerControllers;

use App\Entity\Sensor\SensorTrigger;
use App\Entity\User\User;
use App\Repository\Sensor\SensorTriggerRepository;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Traits\HomeAppAPITrait;
use App\Voters\SensorVoter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensor-trigger/')]
class DeleteTriggerController extends AbstractController
{
    use HomeAppAPITrait;

    public function __construct(
        private readonly LoggerInterface $elasticLogger,
    ) {
    }

    /**
     * @throws AccessDeniedException
     */
    #[Route('{sensorTrigger}', name: 'delete-sensor-trigger', methods: [Request::METHOD_DELETE])]
    public function deleteTrigger(SensorTrigger $sensorTrigger, SensorTriggerRepository $sensorTriggerRepository): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException(APIErrorMessages::FORBIDDEN_ACTION);
        }

        try {
            $this->denyAccessUnlessGranted(SensorVoter::CAN_DELETE_TRIGGER, $sensorTrigger);
        } catch (AccessDeniedException) {
            $this->elasticLogger->info(
                sprintf(
                    'User: %d tried to delete sensor trigger with id: %d',
                    $user->getUserID(),
                    $sensorTrigger->getSensorTriggerID()
                ),
                ['sensorTriggerID' => $sensorTrigger->getSensorTriggerID()]
            );
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $deletedSensorTriggerID = $sensorTrigger->getSensorTriggerID();

        $sensorTriggerRepository->remove($sensorTrigger);
        $sensorTriggerRepository->flush();

        $this->elasticLogger->info(
            sprintf(
                'Sensor trigger deleted with id: %d by user: %d',
                $deletedSensorTriggerID,
                $user->getUserID()
            ),
            ['sensorTriggerID' => $deletedSensorTriggerID]
        );

        return $this->sendSuccessfulJsonResponse(['sensorTriggerID' => $deletedSensorTriggerID]);
    }
}
