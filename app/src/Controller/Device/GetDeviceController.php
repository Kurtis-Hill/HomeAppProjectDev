<?php
declare(strict_types=1);

namespace App\Controller\Device;

use App\Builders\Device\DeviceGet\GetDeviceDTOBuilder;
use App\Builders\Device\DeviceResponse\DeviceResponseDTOBuilder;
use App\Entity\Device\Devices;
use App\Entity\User\User;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Exceptions\User\GroupExceptions\GroupNotFoundException;
use App\Exceptions\User\RoomsExceptions\RoomNotFoundException;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Device\GetDevices\DevicesForUserInterface;
use App\Services\Request\PaginationCalculator;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\DeviceVoter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

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

    /**
     * @throws \App\Exceptions\User\GroupExceptions\GroupNotFoundException
     * @throws RoomNotFoundException
     * @throws \App\Exceptions\Sensor\ReadingTypeNotExpectedException
     */
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
                $request->get(RequestQueryParameterHandler::RESPONSE_TYPE, RequestTypeEnum::ONLY->value),
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
            $normalizedResponse = $this->normalize($deviceDTOs);
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

    /**
     * @throws \App\Exceptions\User\GroupExceptions\GroupNotFoundException
     * @throws RoomNotFoundException
     * @throws \App\Exceptions\Sensor\ReadingTypeNotExpectedException
     */
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
        $responseType = $request->get(RequestQueryParameterHandler::RESPONSE_TYPE, RequestTypeEnum::ONLY->value);
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
                ],
                true
            )
        );

        try {
            $normalizedResponse = $this->normalize($deviceDTO, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface $e) {
            $this->logger->error(sprintf(APIErrorMessages::QUERY_FAILURE, 'Get device'));

            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::FAILURE, 'Get device'), ['error' => $e->getMessage()]]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
