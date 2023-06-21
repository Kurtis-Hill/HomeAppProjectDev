import AnalogResponseInterface from "./AnalogResponseInterface"
import HumidityResponseInterface from "./HumidityResponseInterface"
import LatitudeResponseInterface from "./LatitudeResponseInterface"
import TemperatureResponseInterface from "./TemperatureResponseInterface"

export interface SensorReadingTypeResponseInterface {
    analog?: AnalogResponseInterface
    humidity?: HumidityResponseInterface
    temperature?:TemperatureResponseInterface
    latitude?:LatitudeResponseInterface
}