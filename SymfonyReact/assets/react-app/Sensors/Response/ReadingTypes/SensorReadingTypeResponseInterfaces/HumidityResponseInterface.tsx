import SensorResponseInterface from "../../Sensor/SensorResponseInterface";

export default interface HumidityResponseInterface {
    humidityID: number,
    sensor: SensorResponseInterface,
    currentReading: number,
    highReading: number,
    lowReading: number,
    constRecord: boolean,
    updatedAt: string,
}