<?php


namespace App\Controller\Sensors;

use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\Form\SensorForms\AddNewSensorForm;
use App\Services\CardDataServiceUser;
use App\Services\SensorData\SensorDeviceDataService;
use App\Services\SensorData\SensorUserDataService;
use App\Services\UserSensorService;
use App\Traits\API\HomeAppAPIResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/HomeApp/api/sensors", name="devices")
 */
class SensorController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    /**
     * @Route("/update/current-reading", name="update-current-reading")
     * @param Request $request
     * @param SensorDeviceDataService $sensorDataService
     * @return Response
     */
    public function updateSensorsCurrentReading(Request $request, SensorDeviceDataService $sensorDataService): Response
    {
        if (empty($request->request->get('secret')) || empty($request->request->get('sensor-type'))) {
           // dd($request->request->get('secret'), $request->request->get('sensor-type'));
            return $this->sendBadRequestResponse();
        }

        $sensorFormData = $sensorDataService->processSensorReadingUpdateRequest($request);

        if (empty($sensorFormData)) {
            return $this->sendInternelServerErrorResponse();
        }

//        foreach ($sensorFormData as $sensorType => $sensorData) {
//            foreach ($cardSensorData as $sensorObject) {
//                if ($sensorType === $sensorObject::class) {
//                    $sensorForm = $this->createForm($sensorData['formToProcess'], $sensorObject, ['formSensorType' => new $sensorData['object']]);
//                    $handledSensorForm = $sensorDataService->processForm($sensorForm, $sensorData['formData']);
//
//                    if ($handledSensorForm instanceof FormInterface) {
//                        $sensorDataService->processSensorFormErrors($handledSensorForm);
//                    }
//                    continue;
//                }
//            }
//        }

    }


    /**
     * @Route("/add-new-sensor", name="add-new-sensor")
     * @param Request $request
     * @param UserSensorService $sensorService
     * @return JsonResponse
     */
    public function addNewSensor(Request $request, SensorUserDataService $sensorService, CardDataServiceUser $cardDataService): JsonResponse
    {
        $sensorName = $request->get('sensor-name');
        $sensorType = $request->get('sensor-type');
        $deviceNameID = $request->get('device-name');

        if (empty($sensorName || $sensorType)) {
            return $this->sendBadRequestResponse(['errors' => 'Bad request somethings wrong with your form data, if the problem persists log out an back in again']);
        }

        $sensorData = [
            'sensorName' => $sensorName,
            'sensorTypeID' => $sensorType,
            'deviceNameID' => $deviceNameID
        ];

        $newSensor = new Sensors();

        $addNewSensorForm = $this->createForm(AddNewSensorForm::class, $newSensor);

        $handledSensorForm = $sensorService->handleNewSensorFormSubmission($addNewSensorForm, $sensorData);

        if (!empty($sensorService->getUserInputErrors())) {
            return $this->sendBadRequestResponse($sensorService->getUserInputErrors());
        }

        if (!empty($sensorService->getServerErrors())) {
            return $this->sendInternelServerErrorResponse(['errors' => 'Something went wrong please try again']);
        }

        if ($handledSensorForm->getData() instanceof Sensors) {
            $newSensorCard = $cardDataService->createNewSensorCard($handledSensorForm->getData());
            $sensorID = $handledSensorForm->getData()->getSensorNameID();
            $sensorService->handleSensorCreation($newSensor, $newSensorCard, $sensorData);

            if (!empty($sensorService->getUserInputErrors())) {
                return $this->sendBadRequestResponse($sensorService->getUserInputErrors());
            }

            return $this->sendCreatedResourceResponse(['sensorNameID' => $sensorID]);
        }

        return $this->sendBadRequestResponse();
    }

    /**
     * @Route("/types", name="get-sensor-types")
     * @return JsonResponse
     */
    public function returnAllSensorTypes(): Response
    {
        $sensorTypes = $this->getDoctrine()->getManager()->getRepository(SensorType::class)->findAll();

        $encoders = [new JsonEncoder()];
        $normaliser = [new ObjectNormalizer()];

        $serializer = new Serializer($normaliser, $encoders);

        return $this->sendSuccessfulResponse($serializer->serialize($sensorTypes, 'json'));
    }


}
