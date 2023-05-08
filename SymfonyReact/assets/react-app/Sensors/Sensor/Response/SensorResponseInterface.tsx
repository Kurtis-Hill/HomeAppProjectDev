import UserResponseInterface from '../../../User/Response/UserResponseInterface';
import { DeviceResponseInterface } from '../../../Devices/Response/DeviceResponseInterface';
import { SensorTypeResponseInterface } from '../../SensorType/Response/SensorTypeResponseInterface';
import AnalogResponseInterface from '../../ReadingType/Response/SensorReadingTypeResponseInterfaces/AnalogResponseInterface';
import HumidityResponseInterface from '../../ReadingType/Response/SensorReadingTypeResponseInterfaces/HumidityResponseInterface';
import TemperatureResponseInterface from '../../ReadingType/Response/SensorReadingTypeResponseInterfaces/TemperatureResponseInterface';
import LatitudeResponseInterface from '../../ReadingType/Response/SensorReadingTypeResponseInterfaces/LatitudeResponseInterface';

export default interface SensorResponseInterface {
    sensorID: number,
    sensorName: string,
    createdBy?: UserResponseInterface,
    device?: DeviceResponseInterface,
    sensorType?: SensorTypeResponseInterface,
    sensorReadingTypes?: Array<
        AnalogResponseInterface
        |HumidityResponseInterface
        |TemperatureResponseInterface
        |LatitudeResponseInterface
    >,
}