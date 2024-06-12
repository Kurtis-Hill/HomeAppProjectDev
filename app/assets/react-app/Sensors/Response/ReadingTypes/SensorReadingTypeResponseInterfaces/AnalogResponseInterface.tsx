import { ReadingTypesEnum } from "../../../Enum/ReadingTypesEnum";
import SensorResponseInterface from "../../Sensor/SensorResponseInterface";

export default interface AnalogResponseInterface {
    analogID: number,
    baseReadingTypeID: number,
    sensor: SensorResponseInterface,
    currentReading: number,
    highReading: number,
    lowReading: number,
    constRecord: boolean,
    updatedAt: string,
    sensorType: string,
    readingType: ReadingTypesEnum.analog,
}
