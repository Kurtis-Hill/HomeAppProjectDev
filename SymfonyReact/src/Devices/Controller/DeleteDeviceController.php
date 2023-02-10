<?php

namespace App\Devices\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Devices\Builders\DeviceUpdate\DeviceDTOBuilder;
use App\Devices\Builders\DeviceUpdate\DeviceUpdateResponseDTOBuilder;
use App\Devices\DeviceServices\DeleteDevice\DeleteDeviceServiceInterface;
use App\Devices\Entity\Devices;
use App\Devices\Voters\DeviceVoter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-devices/', name: 'delete-user-devices')]
class DeleteDeviceController extends AbstractController
{
    use HomeAppAPITrait;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $elasticLogger)
    {
        $this->logger = $elasticLogger;
    }

    #[
        Route(
            path: '{deviceNameID}/delete-device',
            name: 'delete-esp-device',
            methods: [Request::METHOD_POST]
        )
    ]
    public function deleteDevice(
        Devices $deviceToDelete,
        DeleteDeviceServiceInterface $deleteDeviceBuilder,
    ): JsonResponse {
        try {
            $this->denyAccessUnlessGranted(DeviceVoter::DELETE_DEVICE, $deviceToDelete);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $deviceDeletedID = $deviceToDelete->getDeviceID();
        $deviceDeleteSuccess = $deleteDeviceBuilder->deleteDevice($deviceToDelete);
        if ($deviceDeleteSuccess !== true) {
            $this->logger->error(sprintf(APIErrorMessages::QUERY_FAILURE, 'Delete device'));
            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::FAILURE, 'Delete device')]);
        }

        $deviceDTO = DeviceUpdateResponseDTOBuilder::buildDeletedDeviceResponseDTO($deviceToDelete);
        try {
            $normalizedResponse = $this->normalizeResponse($deviceDTO);
        } catch (ExceptionInterface $e) {
            $normalizedResponse = null;
        }

        $this->logger->info('device deleted successfully id: ' . $deviceDeletedID, ['user' => $this->getUser()?->getUserIdentifier()]);

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
