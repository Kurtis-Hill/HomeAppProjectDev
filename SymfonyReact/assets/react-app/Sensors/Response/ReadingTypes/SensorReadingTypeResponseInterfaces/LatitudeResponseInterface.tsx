import SensorResponseInterface from "../../Sensor/SensorResponseInterface";
import { ReadingTypesEnum } from '../../../../Enum/ReadingTypesEnum';

export default interface LatitudeResponseInterface {
    latitudeID: number,
    sensor: SensorResponseInterface,
    currentReading: number,
    highReading: number,
    lowReading: number,
    constRecord: boolean,
    updatedAt: string,
    readingType: string,
    sensorType: ReadingTypesEnum.latitude,
}