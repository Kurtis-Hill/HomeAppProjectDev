<?php

namespace App\Devices\Controller;

use App\API\APIErrorMessages;
use App\API\CommonURL;
use App\API\Traits\HomeAppAPITrait;
use App\Devices\DeviceServices\DeleteDevice\DeleteDeviceBuilderInterface;
use App\Devices\Entity\Devices;
use App\Devices\Voters\DeviceVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
        Devices $deviceToUpdate,
        DeleteDeviceBuilderInterface $deleteDeviceBuilder,
    ): JsonResponse {
        try {
            $this->denyAccessUnlessGranted(DeviceVoter::DELETE_DEVICE, $deviceToUpdate);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $deviceDeleteSuccess = $deleteDeviceBuilder->deleteDevice($deviceToUpdate);

        if ($deviceDeleteSuccess !== true) {
            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Delete device')]);
        }

        return $this->sendSuccessfulJsonResponse();
    }
}
