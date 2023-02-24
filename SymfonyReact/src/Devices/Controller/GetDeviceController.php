<?php

namespace App\Devices\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Devices\Builders\DeviceUpdate\DeviceResponseDTOBuilder;
use App\Devices\DeviceServices\GetDevices\GetDevicesForUserInterface;
use App\Devices\Entity\Devices;
use App\Devices\Voters\DeviceVoter;
use App\User\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-devices/', name: 'get-user-devices')]
class GetDeviceController extends AbstractController
{
    use HomeAppAPITrait;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $elasticLogger)
    {
        $this->logger = $elasticLogger;
    }

    #[Route('{deviceID}', name: 'get-user-devices_single', methods: [Request::METHOD_GET])]
    public function getDeviceByID(Devices $devices): Response
    {
        try {
            $this->denyAccessUnlessGranted(DeviceVoter::GET_DEVICE, $devices);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $deviceDTO = DeviceResponseDTOBuilder::buildDeviceFullDetailsResponseDTO($devices);

        try {
            $normalizedResponse = $this->normalizeResponse($deviceDTO);
        } catch (ExceptionInterface $e) {
            $this->logger->error(sprintf(APIErrorMessages::QUERY_FAILURE, 'Get device'));

            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::FAILURE, 'Get device'), ['error' => $e->getMessage()]]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }

    #[Route('all', name: 'get-user-devices_multiple', methods: [Request::METHOD_GET])]
    public function getAllDevices(Request $request, GetDevicesForUserInterface $getDevicesForUser): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $limit = $request->get('limit', GetDevicesForUserInterface::MAX_DEVICE_RETURN_SIZE);
        $limit = min($limit, GetDevicesForUserInterface::MAX_DEVICE_RETURN_SIZE);

        $offset = $request->get('offset', 0);

        $devices = $getDevicesForUser->getDevicesForUser(
            $user,
            $limit,
            $offset,
        );

        $deviceDTOs = [];
        foreach ($devices as $device) {
            $deviceDTOs[] = DeviceResponseDTOBuilder::buildDeviceFullDetailsResponseDTO($device);
        }

        try {
            $normalizedResponse = $this->normalizeResponse($deviceDTOs);
        } catch (ExceptionInterface $e) {
            $this->logger->error(sprintf(APIErrorMessages::QUERY_FAILURE, 'Get device'));

            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::FAILURE, 'Get device'), ['error' => $e->getMessage()]]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
