<?php


namespace App\DTOs\Sensors;

use App\DTOs\Sensors\AbstractCardSensorDTO;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class StandardSensorCardDataDTO extends AbstractCardSensorDTO
{
    /**
     * @var string
     */
    private string $sensorName;

    /**
     * @var string
     */
    private string $sensorType;

    /**
     * @var string
     */
    private string $sensorRoom;

    /**
     * @var string
     */
    private string $cardIcon;

    /**
     * @var string
     */
    private string $cardColour;

    /**
     * @var int
     */
    private int $cardViewID;

    /**
     * @var array
     */
    private array $sensorData = [];


    /**
     * CardDataDTO constructor
     * @param StandardSensorTypeInterface $cardDTOData
     */
    public function __construct(StandardSensorTypeInterface $cardDTOData)
    {
        $this->filterSensorTypes($cardDTOData);
        $this->setCardViewData($cardDTOData);

        if (empty($this->cardViewID) || empty($this->sensorName) || empty($this->cardIcon) || empty($this->sensorType) || empty($this->sensorRoom) || empty($this->cardColour)) {
            throw new \RuntimeException('Some card data is missing');
        }
    }

    protected function setCardViewData(StandardSensorTypeInterface $cardDTOData): void
    {
        $this->cardViewID = $cardDTOData->getCardViewObject()->getCardViewID();

        $this->sensorName =$cardDTOData->getCardViewObject()->getSensorNameID()->getSensorName();

        $this->cardIcon = $cardDTOData->getCardViewObject()->getCardIconID()->getIconName();

        $this->sensorType = $cardDTOData->getCardViewObject()->getSensorNameID()->getSensorTypeID()->getSensorType();

        $this->sensorRoom = $cardDTOData->getCardViewObject()->getSensorNameID()->getDeviceNameID()->getRoomObject()->getRoom();

        $this->cardColour = $cardDTOData->getCardViewObject()->getCardColourID()->getColour();
    }

    /**
     * @param StandardReadingSensorInterface $sensorTypeObject
     * @param string $type
     * @param string|null $symbol
     */
    protected function setSensorData(StandardReadingSensorInterface $sensorTypeObject, string $type, string $symbol = null): void
    {
        try {
            $this->sensorData[] = [
                'sensorType' => $type,
                'highReading' => is_float($sensorTypeObject->getHighReading()) ? number_format($sensorTypeObject->getHighReading(), 2) : $sensorTypeObject->getHighReading(),
                'lowReading' => is_float($sensorTypeObject->getLowReading()) ? number_format($sensorTypeObject->getLowReading(), 2): $sensorTypeObject->getLowReading(),
                'currentReading' => is_float($sensorTypeObject->getCurrentReading()) ?  number_format($sensorTypeObject->getCurrentReading(), 2) : $sensorTypeObject->getCurrentReading(),
                'getCurrentHighDifference' => is_float($sensorTypeObject->getMeasurementDifferenceHighReading()) ? number_format($sensorTypeObject->getMeasurementDifferenceHighReading(), 2) : $sensorTypeObject->getMeasurementDifferenceHighReading(),
                'getCurrentLowDifference' => is_float($sensorTypeObject->getMeasurementDifferenceLowReading()) ? number_format($sensorTypeObject->getMeasurementDifferenceLowReading(), 2) : $sensorTypeObject->getMeasurementDifferenceLowReading(),
                'readingSymbol' => $symbol,
                'time' => $sensorTypeObject->getTime()->format('d-m H:i:s')
            ];
        } catch (\Exception $exception) {
            error_log($exception);

        }
    }

    /**
     * @return array
     */
    public function getSensorData(): array
    {
        return $this->sensorData;
    }

    /**
     * @return string
     */
    public function getSensorName(): string
    {
        return $this->sensorName;
    }

    /**
     * @return string
     */
    public function getSensorType(): string
    {
        return $this->sensorType;
    }

    /**
     * @return string
     */
    public function getSensorRoom(): string
    {
        return $this->sensorRoom;
    }

    /**
     * @return string
     */
    public function getCardIcon(): string
    {
        return $this->cardIcon;
    }

    /**
     * @return string
     */
    public function getCardColour(): string
    {
        return $this->cardColour;
    }

    /**
     * @return int
     */
    public function getCardViewID(): int
    {
        return $this->cardViewID;
    }

}
