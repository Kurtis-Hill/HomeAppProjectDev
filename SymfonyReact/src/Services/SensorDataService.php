<?php


namespace App\Services;


use App\DTOs\Sensors\CardViewSensorFormDTO;
use App\Entity\Card\CardColour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\CardView;
use App\Entity\Card\Icons;
use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use App\HomeAppSensorCore\AbstractHomeAppSensorServiceCore;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class SensorDataService extends AbstractHomeAppSensorServiceCore
{
    protected const SENSOR_DATA = [
        Sensors::TEMPERATURE => [
            'alias' => 'temp',
            'object' => Temperature::class
        ],
        Sensors::HUMIDITY => [
            'alias' => 'humid',
            'object' => Humidity::class
        ],
        Sensors::ANALOG => [
            'alias' => 'analog',
            'object' => Analog::class
        ],
        Sensors::LATITUDE => [
            'alias' => 'lat',
            'object' => Latitude::class
        ]
    ];

    /**
     * @var array
     */
    protected array $serverErrors = [];

    /**
     * @var array
     */
    protected array $userInputErrors = [];


    private function getUserSelectionData(): ?array
    {
        try {
            $icons = $this->em->getRepository(Icons::class)->getAllIcons();
            $colours = $this->em->getRepository(CardColour::class)->getAllColours();
            $states = $this->em->getRepository(Cardstate::class)->getAllStates();
        } catch (\PDOException | \Exception $e) {
            error_log($e->getMessage());
            $this->serverErrors['Failed to process user selection queries'];
        }

        if (empty($icons) || empty($colours) || empty($states)) {
            return null;
        }

        return ['icons' => $icons, 'colours' => $colours, 'states' => $states];
    }
    /**
     * @param Request $request
     * @param int $sensorType
     * @return array
     */
    public function prepareSensorFormData(Request $request, string $sensorType): array
    {
        foreach (self::STANDARD_SENSOR_TYPE_DATA as $sensorComponents) {
            if (strtolower($sensorType) === $sensorComponents['alias']) {
                foreach ($sensorComponents['forms'] as $readingType => $form) {
                    $sensorFormsData[$readingType] = [
                        'form' => $form,
                        'formData' => [
                            'highReading' => $request->get($readingType.'HighReading')
                                ?? $this->userErrors[] = ucfirst($readingType). 'CurrentReading Failed',
                            'lowReading' => $request->get($readingType.'LowReading')
                                ?? $this->userErrors[] = ucfirst($readingType). 'CurrentReading Failed',
                            'constRecord' => $request->get($readingType.'ConstRecord')
                                ?? $this->userErrors[] = ucfirst($readingType). 'CurrentReading Failed',
                        ],
                    ];
                }
            }
            // When adding new sensor types other than the standard add the new sensor type const array to
            // this method here to get the form data processed use the str comparison against the new array
        }

        try {
            foreach (self::SENSOR_DATA as $sensorTypeKey => $typeData) {
                $sensorTypeKey = lcfirst($sensorTypeKey);
                if (!empty($sensorFormsData[$sensorTypeKey])) {
                    if (empty($typeData['object'])) {
                        throw new \RuntimeException('failed to find object for '. $sensorTypeKey . 'the form failed to process');
                    }
                    $sensorFormsData[$sensorTypeKey]['object'] = $typeData['object'];
                }
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = 'Failed to find object for the type of sensor requested, this reading type may be unsupported';
        }

        return $sensorFormsData ?? [];
    }



    /**
     * @param string $cardViewID
     * @return CardViewSensorFormDTO|null
     */
    public function getCardViewFormData(string $cardViewID): ?CardViewSensorFormDTO
    {
        $cardData = $this->em->getRepository(CardView::class)->getCardSensorFormData(['id' => $cardViewID], self::STANDARD_SENSOR_TYPE_DATA);

        if ($cardData instanceof StandardSensorTypeInterface) {
            $userSelectionData = $this->getUserSelectionData();

            if ($userSelectionData === null) {
                return null;
            }

            $cardViewFormDTO = new CardViewSensorFormDTO($cardData, $userSelectionData);
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
        try {
            $usersCurrentCardData = $this->em->getRepository(CardView::class)->getUsersCurrentlySelectedCardData(['id' => $cardViewData, 'userID' =>  $this->getUserID()], self::SENSOR_DATA);
        } catch(\PDOException | \Exception $e){
            $this->serverErrors[] = 'Query error trying to find users card data';
            error_log($e->getMessage());
        }

        return $usersCurrentCardData ?? [];
    }


    /**
     * @param FormInterface $form
     * @param array $formData
     * @return bool|FormInterface
     */
    public function processForm(FormInterface $form, array $formData): ?FormInterface
    {
        $form->submit($formData);

        if ($form->isSubmitted() && $form->isValid()) {
            $validFormData = $form->getData();

            try {
                $this->em->persist($validFormData);
            } catch (\PDOException | \Exception $e) {
                error_log($e->getMessage());
                $this->serverErrors[] = 'Object persistence failed';
            }

            return null;
        }

        return $form;
    }

    /**
     * @param FormInterface $form
     */
    public function processSensorFormErrors(FormInterface $form): void
    {
        foreach ($form->getErrors(true, true) as $error) {
            $this->userInputErrors[] = $error->getMessage();
        }
    }

    /**
     * @return array
     */
    public function getUserInputErrors(): array
    {
        return $this->userInputErrors;
    }

    /**
     * @return array
     */
    #[Pure] public function getServerErrors(): array
    {
        return array_merge($this->getUserErrors(), $this->serverErrors);
    }
}
