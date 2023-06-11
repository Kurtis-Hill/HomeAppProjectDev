import SensorResponseInterface from "../../Sensor/SensorResponseInterface";

type LatitudeString = 'Latitude';

export default interface LatitudeResponseInterface {
    latitudeID: number,
    sensor: SensorResponseInterface,
    currentReading: number,
    highReading: number,
    lowReading: number,
    constRecord: boolean,
    updatedAt: string,
    readingType: string,
    sensorType: LatitudeString,
}