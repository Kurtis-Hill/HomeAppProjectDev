import AnalogResponseInterface from "./AnalogResponseInterface"
import HumidityResponseInterface from "./HumidityResponseInterface"
import LatitudeResponseInterface from "./LatitudeResponseInterface"
import TemperatureResponseInterface from "./TemperatureResponseInterface"
import RelayResponseInterface from './RelayResponseInterface';
import MotionResponseInterface from './MotionResponseInterface';

export interface SensorReadingTypeResponseInterface {
    readingTypes: IndividualSensorReadingTypeResponseInterface,
    sensorType: string,
}

export interface IndividualSensorReadingTypeResponseInterface {
    analog?: AnalogResponseInterface
    humidity?: HumidityResponseInterface
    temperature?:TemperatureResponseInterface
    latitude?:LatitudeResponseInterface
    relay?: RelayResponseInterface
    motion?: MotionResponseInterface
}
