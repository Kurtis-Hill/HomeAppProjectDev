<?php

namespace App\User\Controller\RoomControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Devices\Builders\DeviceGet\GetDeviceDTOBuilder;
use App\Devices\DeviceServices\GetDevices\DevicesForUserInterface;
use App\Devices\DTO\Request\GetDeviceRequestDTO;
use App\User\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-rooms/', name: 'get-user-rooms')]
class GetRoomsController extends AbstractController
{
    use HomeAppAPITrait;

    use ValidatorProcessorTrait;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $elasticLogger)
    {
        $this->logger = $elasticLogger;
    }

    #[Route('all', name: 'get-user-rooms_multiple', methods: [Request::METHOD_GET])]
    public function getAllUserRooms(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }
        $limit = $request->get('limit', DevicesForUserInterface::MAX_DEVICE_RETURN_SIZE);
        $offset = $request->get('offset', 0);

        $getDeviceRequestDTO = new GetDeviceRequestDTO(
            is_numeric($limit) ? (int) $limit : $limit,
            is_numeric($offset) ? (int) $offset : $offset,
        );

        $validationErrors = $validator->validate($getDeviceRequestDTO);
        if ($this->checkIfErrorsArePresent($validationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($validationErrors));
        }

//        $getDeviceDTO = GetDeviceDTOBuilder::buildGetDeviceDTO(
//            $user,
//            $getDeviceRequestDTO,
//            RequestDTOBuilder::buildRequestTypeDTO(RequestDTOBuilder::REQUEST_TYPE_FULL),
//        );

        $devices = $getDevicesForUser->getDevicesForUser($getDeviceDTO);

        $deviceResponseDTO = DeviceResponseDTOBuilder::buildDeviceResponseDTO(
            $devices,
            RequestDTOBuilder::buildRequestTypeDTO(RequestDTOBuilder::REQUEST_TYPE_FULL),
        );

        return $this->sendJsonResponse($deviceResponseDTO);
    }
}
