<?php

namespace App\Sensors\Controller\TriggerControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Sensors\Entity\SensorTrigger;
use App\Sensors\Repository\SensorTriggerRepository;
use App\Sensors\Voters\SensorVoter;
use App\User\Entity\User;
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
    #[Route('{sensorTrigger}/delete', name: 'delete-sensor-trigger', methods: [Request::METHOD_DELETE])]
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
