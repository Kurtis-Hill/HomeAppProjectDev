<?php

namespace App\Controller\Device;

use App\Builders\Device\DeviceResponse\DeviceResponseDTOBuilder;
use App\Builders\Device\DeviceUpdate\DeviceDTOBuilder;
use App\DTOs\Device\Request\NewDeviceRequestDTO;
use App\Entity\User\User;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Exceptions\Device\DeviceCreationFailureException;
use App\Exceptions\User\GroupExceptions\GroupNotFoundException;
use App\Exceptions\User\RoomsExceptions\RoomNotFoundException;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Device\DeleteDevice\DeleteDeviceServiceInterface;
use App\Services\Device\NewDevice\NewDeviceHandlerInterface;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\DeviceVoter;
use Doctrine\ORM\Exception\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-devices', name: 'add-new-user-devices')]
class AddNewDeviceController extends AbstractController
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
     * @throws RoomNotFoundException
     * @throws ORMException
     * @throws DeviceCreationFailureException
     * @throws GroupNotFoundException
     */
    #[Route('', name: 'add-new-esp-device', methods: [Request::METHOD_POST])]
    public function addNewDevice(
        Request $request,
        ValidatorInterface $validator,
        NewDeviceHandlerInterface $newDeviceHandler,
        DeleteDeviceServiceInterface $deleteDeviceHandler,
        DeviceResponseDTOBuilder $deviceResponseDTOBuilder,
        DeviceDTOBuilder $deviceDTOBuilder,
    ): JsonResponse {
        $newDeviceRequestDTO = new NewDeviceRequestDTO();
        try {
            $this->deserializeRequest(
                $request->getContent(),
                NewDeviceRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $newDeviceRequestDTO]
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([], APIErrorMessages::FORMAT_NOT_SUPPORTED);
        }
        $requestValidationErrors = $validator->validate($newDeviceRequestDTO);
        if ($this->checkIfErrorsArePresent($requestValidationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($requestValidationErrors));
        }

        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $request->get(RequestQueryParameterHandler::RESPONSE_TYPE, RequestTypeEnum::SENSITIVE_ONLY->value),
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        try {
            $newDeviceCheckDTO = $deviceDTOBuilder->buildNewDeviceDTOFromNewDeviceRequest(
                $newDeviceRequestDTO,
                $user
            );
        } catch (GroupNotFoundException|RoomNotFoundException $e) {
            return $this->sendNotFoundResponse([$e->getMessage()]);
        }

        try {
            $this->denyAccessUnlessGranted(DeviceVoter::ADD_NEW_DEVICE, $newDeviceCheckDTO);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $errors = $newDeviceHandler->processNewDevice($newDeviceCheckDTO);
        if (!empty($errors)) {
            return $this->sendBadRequestJsonResponse($errors);
        }

        $device = $newDeviceCheckDTO->getNewDevice();
        $deviceSaved = $newDeviceHandler->saveDevice($device, true);
        if ($deviceSaved === false) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::FAILED_TO_SAVE_OBJECT, 'device')]);
        }

        $newDeviceResponseDTO = $deviceResponseDTOBuilder->buildDeviceResponseDTOWithDevicePermissions($device);
        try {
            $response = $this->normalize($newDeviceResponseDTO, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface $e) {
            $deleteDeviceHandler->deleteDevice($device);
            $this->logger->error($e, $e->getTrace());

            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }
        $this->logger->info('new device created with id: ' . $device->getDeviceID(), ['user' => $user->getUserIdentifier()]);

        return $this->sendCreatedResourceJsonResponse($response);
    }
}
