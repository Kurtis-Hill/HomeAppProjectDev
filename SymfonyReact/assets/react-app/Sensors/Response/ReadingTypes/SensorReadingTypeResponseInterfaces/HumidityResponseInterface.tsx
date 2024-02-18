import SensorResponseInterface from "../../Sensor/SensorResponseInterface";

type HumidityString = 'humidity';

export default interface HumidityResponseInterface {
    humidityID: number,
    sensor: SensorResponseInterface,
    currentReading: number,
    highReading: number,
    lowReading: number,
    constRecord: boolean,
    updatedAt: string,
    readingType: string,
    sensorType: HumidityString,
}