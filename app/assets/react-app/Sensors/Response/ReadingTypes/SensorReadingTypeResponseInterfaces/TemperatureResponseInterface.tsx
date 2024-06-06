import SensorResponseInterface from "../../Sensor/SensorResponseInterface";

export default interface TemperatureResponseInterface {
    temperatureID: number,
    baseReadingTypeID: number,
    sensor: SensorResponseInterface,
    currentReading: number,
    highReading: number,
    lowReading: number,
    constRecord: boolean,
    updatedAt: string,
    readingType: string,
    sensorType: string
}
