<?php

namespace App\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\Sensors\DTO\Internal\Sensor\NewSensorDTO;
use App\Sensors\DTO\Request\AddNewSensorRequestDTO;
use App\Sensors\Entity\SensorType;
use App\Sensors\Repository\ORM\Sensors\SensorTypeRepositoryInterface;
use App\Sensors\SensorDataServices\DeleteSensorService\DeleteSensorService;
use App\Sensors\SensorDataServices\NewSensor\NewSensorCreationServiceInterface;
use App\Sensors\SensorDataServices\NewSensor\ReadingTypeCreation\SensorReadingTypeCreationInterface;
use App\Sensors\Voters\SensorVoter;
use App\UserInterface\Services\Cards\CardCreation\CardCreationServiceInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensors', name: 'new-sensor')]
class AddNewSensorController extends AbstractController
{
    use HomeAppAPITrait;

    #[Route('/add-new-sensor', name: 'add-new-sensor', methods: [Request::METHOD_POST])]
    public function addNewSensor(
        Request $request,
        NewSensorCreationServiceInterface $newSensorCreationService,
        SensorReadingTypeCreationInterface $readingTypeCreation,
        DeviceRepositoryInterface $deviceRepository,
        SensorTypeRepositoryInterface $sensorTypeRepository,
        CardCreationServiceInterface $cardCreationService,
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

        $requestValidationErrors = $newSensorCreationService->validateNewSensorRequestDTO($newSensorRequestDTO);

        if (!empty($requestValidationErrors)) {
            return $this->sendBadRequestJsonResponse($requestValidationErrors);
        }

        try {
            $device = $deviceRepository->findOneById($newSensorRequestDTO->getDeviceNameID());
        } catch (NonUniqueResultException | ORMException) {
            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::CONTACT_SYSTEM_ADMIN, 'device query failed')]);
        }

        if (!$device instanceof Devices) {
            return $this->sendBadRequestJsonResponse([
                sprintf(
                    APIErrorMessages::OBJECT_NOT_FOUND,
                    'Device',
                ),
            ]);
        }
        try {
            $sensorType = $sensorTypeRepository->findOneById($newSensorRequestDTO->getSensorTypeID());
        } catch (ORMException) {
            return $this->sendBadRequestJsonResponse([
                sprintf(
                    APIErrorMessages::OBJECT_NOT_FOUND,
                    'Sensor Type',
                ),
            ]);
        }
        if (!$sensorType instanceof SensorType) {
            return $this->sendBadRequestJsonResponse([
                sprintf(
                    APIErrorMessages::OBJECT_NOT_FOUND,
                    'SensorType',
                ),
            ]);
        }

        $newSensorDTO = new NewSensorDTO(
            $newSensorRequestDTO->getSensorName(),
            $sensorType,
            $device,
            $this->getUser()
        );
        try {
            $this->denyAccessUnlessGranted(SensorVoter::ADD_NEW_SENSOR, $newSensorDTO);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $sensor = $newSensorCreationService->createNewSensor($newSensorDTO);
        $sensorCreationErrors = $newSensorCreationService->validateSensor($sensor);

        if (!empty($sensorCreationErrors)) {
            return $this->sendBadRequestJsonResponse($sensorCreationErrors);
        }

        $saveSensor = $newSensorCreationService->saveNewSensor($sensor);

        if ($saveSensor !== true) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_SAVE_DATA]);
        }

        $sensorReadingTypeCreationErrors = $readingTypeCreation->handleSensorReadingTypeCreation($sensor);

        if (!empty($sensorReadingTypeCreationErrors)) {
            $deleteSensorService->deleteSensor($sensor);

            return $this->sendBadRequestJsonResponse($sensorReadingTypeCreationErrors);
        }

        $errors = $cardCreationService->createUserCardForSensor($sensor, $this->getUser());

        if (!empty($errors)) {
            return $this->sendInternalServerErrorJsonResponse($errors);
        }

        $sensorID = $sensor->getSensorNameID();

        return $this->sendCreatedResourceJsonResponse(['sensorNameID' => $sensorID]);
    }
}
