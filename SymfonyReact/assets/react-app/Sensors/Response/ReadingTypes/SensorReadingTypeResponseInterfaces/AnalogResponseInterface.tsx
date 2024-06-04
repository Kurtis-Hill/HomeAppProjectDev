import SensorResponseInterface from "../../Sensor/SensorResponseInterface";

type AnalogString = 'analog';

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
    readingType: AnalogString,
}
