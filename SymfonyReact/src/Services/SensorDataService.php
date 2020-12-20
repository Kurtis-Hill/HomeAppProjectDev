<?php


namespace App\Services;


use App\DTOs\Sensors\CardSensorFormDTO;
use App\Entity\Card\CardColour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\Icons;
use App\Entity\Cardview;
use App\Entity\Sensors\SensorType;
use App\Form\CardViewForms\DallasTempCardModalForm;
use App\Form\CardViewForms\DHTHumidCardModalForm;
use App\Form\CardViewForms\DHTTempCardModalForm;
use App\Form\CardViewForms\SoilFormType;
use App\HomeAppCore\HomeAppCoreAbstract;
use Symfony\Component\HttpFoundation\Request;

class SensorDataService extends HomeAppCoreAbstract
{
    /**
     * @param Request $request
     * @param array $cardSensorData
     * @param string $sensorType
     * @return array
     */
    public function prepareSensorFormData(Request $request, array $cardSensorData, string $sensorType): array
    {
        $formData = [
            'highSensorReading' => $request->get('firstSensorHighReading'),
            'lowSensorReading' => $request->get('firstSensorLowReading'),
            'constrecord' => $request->get('constRecord')
        ];

        switch ($sensorType) {
            case SensorType::DALLAS_TEMPERATURE:
                return [
                    'object' => $cardSensorData['temp'],
                    'formData' => $formData,
                    'formClass' => DallasTempCardModalForm::class
                ];
                break;

            case SensorType::SOIL_SENSOR:
                return [
                    'object' => $cardSensorData['analog'],
                    'formData' => $formData,
                    'formClass' => SoilFormType::class
                ];
                break;

            case SensorType::DHT_SENSOR:
                $secondFormData = [
                    'highSensorReading' => $request->get('secondSensorHighReading'),
                    'lowSensorReading' => $request->get('secondSensorLowReading'),
                    'constrecord' => $request->get('secondConstRecord')
                ];
                return [
                    'object' => $cardSensorData['temp'],
                    'secondObject' => $cardSensorData['humid'],
                    'formData' => $formData,
                    'secondFormData' => $secondFormData,
                    'formClass' => DHTTempCardModalForm::class,
                    'secondFormClass' => DHTHumidCardModalForm::class
                ];
                break;

            default: return [];
        }
    }


    /**
     * @param array $cardSensorFormData
     * @return array
     */
    public function getFormData(array $cardSensorFormData): array
    {
        $icons = $this->em->getRepository(Icons::class)->getAllIcons();
        $colours = $this->em->getRepository(CardColour::class)->getAllColours();
        $states = $this->em->getRepository(Cardstate::class)->getAllStates();

        $formDTO = new CardSensorFormDTO($cardSensorFormData);

        return ['cardSensorData' => $formDTO, 'icons' => $icons, 'colours' => $colours, 'states' => $states];
    }


    /**
     * @param string $cardViewData
     * @return array
     */
    public function prepareUsersCurrentCardData(string $cardViewData): array
    {
        $usersCurrentCardData = [];

        try {
            $usersCurrentCardData = $this->em->getRepository(Cardview::class)->getUsersCurrentCardData(['id' => $cardViewData, 'userID' =>  $this->getUserID()]);
        } catch(\PDOException $e){
            error_log($e->getMessage());
        } catch(\Exception $e){
            error_log($e->getMessage());
        }

        return $usersCurrentCardData;
    }
}
