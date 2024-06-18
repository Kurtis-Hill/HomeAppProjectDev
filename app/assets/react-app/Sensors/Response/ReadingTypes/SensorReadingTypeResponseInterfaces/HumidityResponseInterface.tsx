import SensorResponseInterface from "../../Sensor/SensorResponseInterface";
import { ReadingTypesEnum } from '../../../Enum/ReadingTypesEnum';

export default interface HumidityResponseInterface {
    humidityID: number,
    baseReadingTypeID: number,
    sensor: SensorResponseInterface,
    currentReading: number,
    highReading: number,
    lowReading: number,
    constRecord: boolean,
    updatedAt: string,
    readingType: string,
    sensorType: ReadingTypesEnum.humidity,
}
