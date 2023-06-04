import SensorResponseInterface from "../../Sensor/SensorResponseInterface";

export default interface TemperatureResponseInterface {
    temperatureID: number,
    sensor: SensorResponseInterface,
    currentReading: number,
    highReading: number,
    lowReading: number,
    constRecord: boolean,
    updatedAt: string,
    type: string,
}