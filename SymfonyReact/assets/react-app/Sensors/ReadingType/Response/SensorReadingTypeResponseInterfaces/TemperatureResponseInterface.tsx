import SensorResponseInterface from "../../../Sensor/Response/SensorResponseInterface";

export default interface TemperatureResponseInterface {
    temperatureID: number,
    sensor: SensorResponseInterface,
    currentReading: number,
    highReading: number,
    lowReading: number,
    constRecord: boolean,
    updatedAt: string,
}