<?php

namespace App\Devices\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Devices\Builders\DeviceUpdate\DeviceDTOBuilder;
use App\Devices\DeviceServices\DeleteDevice\DeleteDeviceBuilderInterface;
use App\Devices\Entity\Devices;
use App\Devices\Voters\DeviceVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-devices', name: 'delete-user-devices')]
class DeleteDeviceController extends AbstractController
{
    use HomeAppAPITrait;

    #[
        Route(
            path: '/delete-device/{deviceNameID}',
            name: 'delete-esp-device',
            methods: [Request::METHOD_POST]
        )
    ]
    public function deleteDevice(
        Devices $deviceToDelete,
        DeleteDeviceBuilderInterface $deleteDeviceBuilder,
    ): JsonResponse {
        try {
            $this->denyAccessUnlessGranted(DeviceVoter::DELETE_DEVICE, $deviceToDelete);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $deviceDeleteSuccess = $deleteDeviceBuilder->deleteDevice($deviceToDelete);

        if ($deviceDeleteSuccess !== true) {
            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Delete device')]);
        }

        $deviceDTO = DeviceDTOBuilder::buildDeviceDTO($deviceToDelete);
        try {
            $normalizedResponse = $this->normalizeResponse($deviceDTO);
        } catch (ExceptionInterface $e) {
            $normalizedResponse = null;
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
