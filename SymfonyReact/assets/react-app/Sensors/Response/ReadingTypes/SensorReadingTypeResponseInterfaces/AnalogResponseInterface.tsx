import SensorResponseInterface from "../../Sensor/SensorResponseInterface";

export default interface AnalogResponseInterface {
    analogID: number,
    sensor: SensorResponseInterface,
    currentReading: number,
    highReading: number,
    lowReading: number,
    constRecord: boolean,
    updatedAt: string,
    type: string,
}