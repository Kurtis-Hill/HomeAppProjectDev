<?php

namespace App\Devices\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Builders\Request\RequestDTOBuilder;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Devices\Builders\DeviceGet\GetDeviceDTOBuilder;
use App\Devices\Builders\DeviceResponse\DeviceResponseDTOBuilder;
use App\Devices\DeviceServices\GetDevices\GetDevicesForUserInterface;
use App\Devices\DTO\Request\GetDeviceRequestDTO;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-device/', name: 'get-user-devices')]
class GetDeviceController extends AbstractController
{
    use HomeAppAPITrait;

    use ValidatorProcessorTrait;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $elasticLogger)
    {
        $this->logger = $elasticLogger;
    }

    #[Route('all', name: 'get-user-devices_multiple', methods: [Request::METHOD_GET])]
    public function getAllDevices(Request $request, GetDevicesForUserInterface $getDevicesForUser, ValidatorInterface $validator): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }
        $limit = $request->get('limit', GetDevicesForUserInterface::MAX_DEVICE_RETURN_SIZE);
        $offset = $request->get('offset', 0);

        $getDeviceRequestDTO = new GetDeviceRequestDTO(
            is_numeric($limit) ? (int) $limit : $limit,
            is_numeric($offset) ? (int) $offset : $offset,
        );

        $validationErrors = $validator->validate($getDeviceRequestDTO);
        if ($this->checkIfErrorsArePresent($validationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($validationErrors));
        }

        $getDeviceDTO = GetDeviceDTOBuilder::buildGetDeviceDTO(
            min(
                $getDeviceRequestDTO->getLimit(),
                GetDevicesForUserInterface::MAX_DEVICE_RETURN_SIZE
            ),
            $getDeviceRequestDTO->getOffset(),
        );

        $devices = $getDevicesForUser->getDevicesForUser(
            $user,
            $getDeviceDTO,
        );

        $deviceDTOs = $getDevicesForUser->handleDeviceResponseDTOCreation($devices);
        try {
            $normalizedResponse = $this->normalizeResponse($deviceDTOs);
        } catch (ExceptionInterface $e) {
            $this->logger->error(
                sprintf(
                    APIErrorMessages::QUERY_FAILURE,
                    'Get device'
                ),
                [
                    'error' => $e->getMessage()
                ]
            );

            return $this->sendBadRequestJsonResponse([
                sprintf(
                    APIErrorMessages::FAILURE,
                    'Get device'
                ),
            ]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }

    #[Route(
        '{deviceID}',
        name: 'get-user-devices-single',
        methods: [Request::METHOD_GET]
    )]
    public function getDeviceByID(
        Devices $devices,
        Request $request,
        ValidatorInterface $validator,
        DeviceResponseDTOBuilder $deviceResponseDTOBuilder,
    ): Response {
        try {
            $this->denyAccessUnlessGranted(DeviceVoter::GET_DEVICE, $devices);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $responseType = $request->query->get('responseType');
        if ($responseType) {
            $requestTypeDTO = RequestDTOBuilder::buildRequestTypeDTO($responseType);
            try {
                $validationErrors = $validator->validate($requestTypeDTO);

                if ($this->checkIfErrorsArePresent($validationErrors)) {
                    return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($validationErrors));
                }

            } catch (ExceptionInterface) {
                return $this->sendInternalServerErrorJsonResponse();
            }
            $deviceDTO = $deviceResponseDTOBuilder->buildDeviceResponseDTO($devices, true);
        } else {
            $deviceDTO = $deviceResponseDTOBuilder->buildDeviceResponseDTO($devices);
        }

        try {
            $normalizedResponse = $this->normalizeResponse($deviceDTO);
        } catch (ExceptionInterface $e) {
            $this->logger->error(sprintf(APIErrorMessages::QUERY_FAILURE, 'Get device'));

            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::FAILURE, 'Get device'), ['error' => $e->getMessage()]]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
