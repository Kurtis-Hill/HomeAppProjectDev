<?php

namespace App\Devices\Controller;

use App\API\APIErrorMessages;
use App\API\CommonURL;
use App\API\Traits\HomeAppAPIResponseTrait;
use App\Devices\DeviceServices\DeleteDevice\DeleteDeviceBuilderInterface;
use App\Devices\DTO\Request\DeleteDeviceRequestDTO;
use App\Devices\Entity\Devices;
use App\Devices\Voters\DeviceVoter;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-devices', name: 'delete-user-devices')]
class DeleteDeviceController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    #[Route('/delete-device', name: 'delete-esp-device', methods: [Request::METHOD_POST])]
    public function deleteDevice(
        Request $request,
        DeleteDeviceBuilderInterface $deleteDeviceBuilder,
    ): JsonResponse {
        $deleteDeviceRequestDTO = new DeleteDeviceRequestDTO();

        $this->deserializeRequest(
            $request->getContent(),
            DeleteDeviceRequestDTO::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $deleteDeviceRequestDTO],
        );

        $requestValidationErrors = $deleteDeviceBuilder->validateDeviceRequestObject($deleteDeviceRequestDTO);

        if (!empty($requestValidationErrors)) {
            return $this->sendBadRequestJsonResponse($requestValidationErrors);
        }
        try {
            $deviceToUpdate = $deleteDeviceBuilder->findDeviceToUpdate($deleteDeviceRequestDTO->getDeviceNameID());
        } catch (NonUniqueResultException | ORMException) {
            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::CONTACT_SYSTEM_ADMIN, 'Failed to find device to update query error')]);
        }

        if (!$deviceToUpdate instanceof Devices) {
            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Device')]);
        }

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
