<?php


namespace App\Controller\Sensors;

use App\Entity\Core\Sensors;
use App\Form\SensorForms\AddNewSensorForm;
use App\Services\SensorService;
use App\Traits\API\HomeAppAPIResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/HomeApp/api/sensors", name="devices")
 */
class SensorController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    /**
     * @Route("/new-sensor/submit-form-data", name="add-new-sensor")
     * @param Request $request
     * @param SensorService $sensorService
     * @return JsonResponse
     */
    public function addNewSensor(Request $request, SensorService $sensorService): JsonResponse
    {
        $sensorName = $request->get('roomID');
        $sensorGroup = $request->get('groupID');
        $sensorRoom = $request->get('sensorName');
        $sensorType = $request->get('sensorType');

        if (empty($sensorGroup || $sensorName || $sensorRoom)) {
            return $this->sendBadRequestResponse(['errors' => 'Bad request somethings wrong with your form data, if the problem persists log out an back in again']);
        }

        $sensorData = [
            'sensorname' => $sensorName,
            'roomdid' => $sensorRoom,
            'groupnameid' => $sensorGroup,
            'sensortypeid' => $sensorType
        ];

        $newSensor = new Sensors();

        $addNewSensorForm = $this->createForm(AddNewSensorForm::class, $newSensor);

        $handledSensorForm = $sensorService->handleNewSensorFormSubmission($sensorData, $addNewSensorForm);

        $errors = $sensorService->getErrors();

        if (!empty($errors)) {
            return $this->sendBadRequestResponse($errors);
        }
        else {
            $sensorID = $handledSensorForm->getData()->getSensornameid();

            return $this->sendCreatedResourceResponse(['sensorNameID' => $sensorID]);
        }


    }
}
