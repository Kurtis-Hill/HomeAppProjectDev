import UserResponseInterface from '../../../User/Response/UserResponseInterface';
import { DeviceResponseInterface } from '../../../Devices/Response/DeviceResponseInterface';
import { SensorTypeResponseInterface } from '../SensorType/SensorTypeResponseInterface';
import {
    IndividualSensorReadingTypeResponseInterface,
    SensorReadingTypeResponseInterface
} from '../ReadingTypes/SensorReadingTypeResponseInterfaces/SensorReadingTypeResponseInterface';
import CardViewResponseInterface from '../../../UserInterface/Cards/Response/CardView/CardViewResponseInterface';

export default interface SensorResponseInterface {
    sensorID: number,
    sensorName: string,
    createdBy?: UserResponseInterface,
    device?: DeviceResponseInterface,
    sensorType?: SensorTypeResponseInterface,
    sensorReadingTypes?: IndividualSensorReadingTypeResponseInterface,
    canEdit?: boolean,
    canDelete?: boolean,
    cardView?: CardViewResponseInterface,
    pinNumber?: number,
    readingInterval?: number,
}
