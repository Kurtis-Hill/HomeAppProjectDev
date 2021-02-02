<?php


namespace App\Controller\Sensors;

use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\Form\SensorForms\AddNewSensorForm;
use App\Services\SensorDataService;
use App\Services\SensorService;
use App\Traits\API\HomeAppAPIResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/submit-form-data", name="add-new-sensor")
     * @param Request $request
     * @param SensorService $sensorService
     * @return JsonResponse
     */
    public function addNewSensor(Request $request, SensorService $sensorService): JsonResponse
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

        if (!empty($sensorService->getUserInputErrors() || $handledSensorForm === null)) {
            return $this->sendBadRequestResponse($sensorService->getUserInputErrors() );
        }


        if (!empty($sensorService->getServerErrors())) {
            return $this->sendInternelServerErrorResponse(['errors' => 'Something went wrong please try again']);
        }

        if ($handledSensorForm->getData() instanceof Sensors) {
            $sensorID = $handledSensorForm->getData()->getSensorNameID();

            return $this->sendCreatedResourceResponse(['sensorNameID' => $sensorID]);
        }

        return $this->sendBadRequestResponse();
    }

    /**
     * @Route("/types", name="get-sensor-types")
     * @param Request $request
     * @param SensorDataService $sensorDataService
     * @return JsonResponse
     */
    public function returnAllSensorTypes(Request $request, SensorDataService $sensorDataService): Response
    {
        $sensorTypes = $this->getDoctrine()->getManager()->getRepository(SensorType::class)->findAll();

        $encoders = [new JsonEncoder()];
        $normaliser = [new ObjectNormalizer()];

        $serializer = new Serializer($normaliser, $encoders);

        if (!empty($sensorDataService->getServerErrors())) {
            return $this->sendInternelServerErrorResponse();
        }

        return $this->sendSuccessfulResponse($serializer->serialize($sensorTypes, 'json'));
    }


}
