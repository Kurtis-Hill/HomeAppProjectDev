<?php


namespace App\Services;


use App\DTOs\Sensors\CardViewSensorFormDTO;
use App\Entity\Card\CardColour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\CardView;
use App\Entity\Card\Icons;
use App\Entity\Sensors\SensorType;
use App\Form\CardViewForms\DallasTempCardModalForm;
use App\Form\CardViewForms\DHTHumidCardModalForm;
use App\Form\CardViewForms\DHTTempCardModalForm;
use App\Form\CardViewForms\SoilFormType;
use App\HomeAppSensorCore\HomeAppSensorServiceCoreAbstract;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use Symfony\Component\HttpFoundation\Request;

class SensorDataService extends HomeAppSensorServiceCoreAbstract
{
    protected array $serverErrors;
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
     * @param array $cardViewID
     * @return array
     */
    public function getCardViewFormData(string $cardViewID): ?CardViewSensorFormDTO
    {
        $cardData = $this->em->getRepository(CardView::class)->getCardSensorFormData(['id' => $cardViewID]);

        if ($cardData instanceof StandardSensorTypeInterface) {
            $icons = $this->em->getRepository(Icons::class)->getAllIcons();
            $colours = $this->em->getRepository(CardColour::class)->getAllColours();
            $states = $this->em->getRepository(Cardstate::class)->getAllStates();

            $formOptions = ['icons' => $icons, 'colours' => $colours, 'states' => $states];

            $cardViewFormDTO = new CardViewSensorFormDTO($cardData, $formOptions);
        }
        else {
            $this->serverErrors[] = 'Query error for card view form';
        }

        return $cardViewFormDTO ?? null;

    }


    /**
     * @param string $cardViewData
     * @return array
     */
    public function prepareUsersCurrentCardData(string $cardViewData): array
    {
        $usersCurrentCardData = [];

        try {
            $usersCurrentCardData = $this->em->getRepository(CardView::class)->getUsersCurrentCardData(['id' => $cardViewData, 'userID' =>  $this->getUserID()]);
        } catch(\PDOException | \Exception $e){
            error_log($e->getMessage());
        }

        return $usersCurrentCardData;
    }

    public function getServerErrors()
    {
        return array_merge($this->getUserErrors(), $this->serverErrors);
    }
}
