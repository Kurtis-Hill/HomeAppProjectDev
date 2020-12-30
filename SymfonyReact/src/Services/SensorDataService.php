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
use App\Entity\Sensors\SensorType;
use App\HomeAppSensorCore\AbstractHomeAppSensorServiceCore;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use Doctrine\ORM\ORMException;
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
        ],

    ];

    /**
     * @var array
     */
    private array $serverErrors = [];

    /**
     * @var array
     */
    private array $userInputErrors = [];


    /**
     * @return array|null
     */
    private function getUserCardSelectionData(): ?array
    {
        $icons = $this->em->getRepository(Icons::class)->getAllIcons();
        $colours = $this->em->getRepository(CardColour::class)->getAllColours();
        $states = $this->em->getRepository(Cardstate::class)->getAllStates();

        if (empty($icons) || empty($colours) || empty($states)) {
            throw new \RuntimeException('user selection data has failed to process');
        }

        return ['icons' => $icons, 'colours' => $colours, 'states' => $states];
    }
    /**
     * @param Request $request
     * @param int $sensorType
     * @return array
     */
    public function prepareSensorFormData(Request $request, SensorType $sensorType): array
    {

//            foreach (self::STANDARD_SENSOR_TYPE_DATA as $sensorComponents) {
//                if (strtolower($sensorType) === $sensorComponents['alias']) {
//                    foreach ($sensorComponents['forms'] as $readingType => $form) {
//                        $sensorFormsData[$readingType] = [
//                            'form' => $form,
//                            'formData' => [
//                                'highReading' => $request->get($readingType.'HighReading')
//                                    ?? $this->fatalErrors[] = ucfirst($readingType). ' HighReading Failed',
//                                'lowReading' => $request->get($readingType.'LowReading')
//                                    ?? $this->fatalErrors[] = ucfirst($readingType). ' LowReading Failed',
//                                'constRecord' => $request->get($readingType.'ConstRecord')
//                                    ?? $this->fatalErrors[] = ucfirst($readingType). ' CurrentReading Failed',
//                            ],
//                        ];
//                    }
//                }
//                // When adding new sensor types other than the standard add the new sensor type const array to
//                // this method here to get the form data processed use the str comparison against the new array
//            }


//            dd($sensorType->);
        //$this->em->getRepository()


            foreach (self::STANDARD_SENSOR_TYPE_DATA as $sensorComponents) {
                //$this->em->getRepository($se)
                if (strtolower($sensorType->getSensorType()) === $sensorComponents['alias']) {
                    foreach ($sensorComponents as $sensorKey => $sensorValues) {

                    }


                    foreach ($sensorComponents['forms'] as $readingType => $form) {
                        $sensorFormsData[$readingType] = [
                            'form' => $form, //make the new standard form
                            'formData' => [
                                'highReading' => $request->get($readingType.'HighReading')
                                    ?? $this->fatalErrors[] = ucfirst($readingType). ' HighReading Failed',
                                'lowReading' => $request->get($readingType.'LowReading')
                                    ?? $this->fatalErrors[] = ucfirst($readingType). ' LowReading Failed',
                                'constRecord' => $request->get($readingType.'ConstRecord')
                                    ?? $this->fatalErrors[] = ucfirst($readingType). ' CurrentReading Failed',
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
        } catch (\RuntimeException $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = $e->getMessage();
        }

        return $sensorFormsData ?? [];
    }


    /**
     * @param string $cardViewID
     * @return CardViewSensorFormDTO|null
     */
    public function getCardViewFormData(string $cardViewID): ?CardViewSensorFormDTO
    {
        try {
            $cardData = $this->em->getRepository(CardView::class)->getCardSensorFormData(['id' => $cardViewID], self::STANDARD_SENSOR_TYPE_DATA);

                $userSelectionData = $this->getUserCardSelectionData();

                if ($userSelectionData === null) {
                    return null;
                }

            if ($cardData instanceof StandardSensorTypeInterface) {
                $cardViewFormDTO = new CardViewSensorFormDTO($cardData, $userSelectionData);
            }
            else {
                $this->serverErrors[] = 'Query error for card view form';
            }
        } catch (\RuntimeException $e) {
            $this->serverErrors[] = $e->getMessage();
        } catch (ORMException $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = 'Card Data Query Failure';
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = 'Failed to prepare card data';
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
        } catch(ORMException | \Exception $e){
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
            } catch (ORMException | \Exception $e) {
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
        return array_merge($this->getFatalErrors(), $this->serverErrors);
    }
}
