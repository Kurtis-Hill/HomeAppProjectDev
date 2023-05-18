<?php

namespace App\Devices\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Exceptions\ValidatorProcessorException;
use App\Common\Services\PaginationCalculator;
use App\Common\Services\RequestQueryParameterHandler;
use App\Common\Services\RequestTypeEnum;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Devices\Builders\DeviceGet\GetDeviceDTOBuilder;
use App\Devices\Builders\DeviceResponse\DeviceResponseDTOBuilder;
use App\Devices\DeviceServices\GetDevices\DevicesForUserInterface;
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

    private RequestQueryParameterHandler $requestQueryParameterHandler;

    public function __construct(LoggerInterface $elasticLogger, RequestQueryParameterHandler $requestQueryParameterHandler)
    {
        $this->logger = $elasticLogger;
        $this->requestQueryParameterHandler = $requestQueryParameterHandler;
    }

    #[Route('all', name: 'get-user-devices-multiple', methods: [Request::METHOD_GET])]
    public function getAllDevices(
        Request $request,
        DevicesForUserInterface $getDevicesForUser,
        DeviceResponseDTOBuilder $deviceResponseDTOBuilder,
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $request->get('responseType', RequestTypeEnum::ONLY->value),
                $request->get('page'),
                $request->get('limit', DevicesForUserInterface::MAX_DEVICE_RETURN_SIZE),
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

        $getDeviceDTO = GetDeviceDTOBuilder::buildGetDeviceDTO(
            min(
                $requestDTO->getLimit(),
                DevicesForUserInterface::MAX_DEVICE_RETURN_SIZE
            ),
            PaginationCalculator::calculateOffset(
                $requestDTO->getLimit(),
                $requestDTO->getPage()
            ),
        );

        $devices = $getDevicesForUser->getDevicesForUser(
            $user,
            $getDeviceDTO,
        );

        $deviceDTOs = [];
        foreach ($devices as $device) {
            $deviceDTOs[] = $deviceResponseDTOBuilder->buildFullDeviceResponseDTO($device);
        }

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
        '{deviceID}/get',
        name: 'get-user-device-single',
        methods: [Request::METHOD_GET]
    )]
    public function getDeviceByID(
        Devices $devices,
        Request $request,
        DeviceResponseDTOBuilder $deviceResponseDTOBuilder,
    ): Response {
        try {
            $this->denyAccessUnlessGranted(DeviceVoter::GET_DEVICE, $devices);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $responseType = $request->get('responseType', RequestTypeEnum::ONLY->value);
        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $responseType
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

        $deviceDTO = $deviceResponseDTOBuilder->buildFullDeviceResponseDTO(
            $devices,
            in_array(
                $responseType,
                [
                    RequestTypeEnum::SENSITIVE_FULL->value,
                    RequestTypeEnum::FULL->value
                ], true)
        );

        try {
            $normalizedResponse = $this->normalizeResponse($deviceDTO, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface $e) {
            $this->logger->error(sprintf(APIErrorMessages::QUERY_FAILURE, 'Get device'));

            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::FAILURE, 'Get device'), ['error' => $e->getMessage()]]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
