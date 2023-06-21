import SensorResponseInterface from "../../Sensor/SensorResponseInterface";

type TemperatureString = 'temperature';
export default interface TemperatureResponseInterface {
    temperatureID: number,
    sensor: SensorResponseInterface,
    currentReading: number,
    highReading: number,
    lowReading: number,
    constRecord: boolean,
    updatedAt: string,
    readingType: string,
    sensorType: string
}