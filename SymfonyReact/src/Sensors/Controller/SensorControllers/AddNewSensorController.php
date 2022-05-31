<?php

namespace App\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Traits\ValidatorProcessorTrait;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\Sensors\Builders\SensorCreationBuilders\NewSensorDTOBuilder;
use App\Sensors\Builders\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\DTO\Internal\Sensor\NewSensorDTO;
use App\Sensors\DTO\Request\AddNewSensorRequestDTO;
use App\Sensors\Entity\SensorType;
use App\Sensors\Exceptions\UserNotAllowedException;
use App\Sensors\Repository\ORM\Sensors\SensorTypeRepositoryInterface;
use App\Sensors\SensorDataServices\DeleteSensorService\DeleteSensorService;
use App\Sensors\SensorDataServices\NewReadingType\SensorReadingTypeCreationInterface;
use App\Sensors\SensorDataServices\NewSensor\NewSensorCreationServiceInterface;
use App\Sensors\Voters\SensorVoter;
use App\UserInterface\Services\Cards\CardCreation\CardCreationHandlerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensors', name: 'new-sensor')]
class AddNewSensorController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[Route('/add-new-sensor', name: 'add-new-sensor', methods: [Request::METHOD_POST])]
    public function addNewSensor(
        Request $request,
        ValidatorInterface $validator,
        NewSensorCreationServiceInterface $newSensorCreationService,
        SensorReadingTypeCreationInterface $readingTypeCreation,
        DeviceRepositoryInterface $deviceRepository,
        SensorTypeRepositoryInterface $sensorTypeRepository,
        CardCreationHandlerInterface $cardCreationService,
        DeleteSensorService $deleteSensorService,
    ): JsonResponse {
        $newSensorRequestDTO = new AddNewSensorRequestDTO();
        try {
            $this->deserializeRequest(
                $request->getContent(),
                AddNewSensorRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $newSensorRequestDTO]
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

        $requestValidationErrors = $validator->validate($newSensorRequestDTO);
        if ($this->checkIfErrorsArePresent($requestValidationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($requestValidationErrors));
        }

        try {
            $deviceObject = $deviceRepository->findOneById($newSensorRequestDTO->getDeviceNameID());
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Device')]);
        }
        if (!$deviceObject instanceof Devices) {
            return $this->sendBadRequestJsonResponse([
                sprintf(
                    APIErrorMessages::OBJECT_NOT_FOUND,
                    'Device',
                ),
            ]);
        }

        try {
            $sensorTypeObject = $sensorTypeRepository->findOneById($newSensorRequestDTO->getSensorTypeID());
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse([
                sprintf(
                    APIErrorMessages::QUERY_FAILURE,
                    'Sensor Type',
                ),
            ]);
        }
        if (!$sensorTypeObject instanceof SensorType) {
            return $this->sendBadRequestJsonResponse([
                sprintf(
                    APIErrorMessages::OBJECT_NOT_FOUND,
                    'SensorType',
                ),
            ]);
        }

        $newSensorDTO = NewSensorDTOBuilder::buildNewSensorDTO(
            $newSensorRequestDTO->getSensorName(),
            $sensorTypeObject,
            $deviceObject,
            $this->getUser()
        );

        try {
            $this->denyAccessUnlessGranted(SensorVoter::ADD_NEW_SENSOR, $newSensorDTO);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }
        try {
            $sensorCreationErrors = $newSensorCreationService->processNewSensor($newSensorDTO);
        } catch (UserNotAllowedException $exception) {
            return $this->sendForbiddenAccessJsonResponse([$exception->getMessage()]);
        }

        if (!empty($sensorCreationErrors)) {
            return $this->sendBadRequestJsonResponse($sensorCreationErrors);
        }

        $sensor = $newSensorDTO->getSensor();

        $saveSensor = $newSensorCreationService->saveSensor($sensor);
        if ($saveSensor !== true) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_SAVE_DATA]);
        }

        $sensorReadingTypeCreationErrors = $readingTypeCreation->handleSensorReadingTypeCreation($sensor);
        if (!empty($sensorReadingTypeCreationErrors)) {
            try {
                $deleteSensorService->deleteSensor($sensor);
            } catch (ORMException) {
                // @TODO add logg
            }

            return $this->sendBadRequestJsonResponse($sensorReadingTypeCreationErrors);
        }

        $errors = $cardCreationService->createUserCardForSensor($sensor, $this->getUser());
        if (!empty($errors)) {
            return $this->sendInternalServerErrorJsonResponse($errors);
        }

        $sensorResponseDTO = SensorResponseDTOBuilder::buildSensorResponseDTO($sensor);
        try {
            $normalizedResponse = $this->normalizeResponse($sensorResponseDTO);
        } catch (ExceptionInterface) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        return $this->sendCreatedResourceJsonResponse($normalizedResponse);
    }
}
