import UserResponseInterface from '../../../User/Response/UserResponseInterface';
import { DeviceResponseInterface } from '../../../Devices/Response/DeviceResponseInterface';
import { SensorTypeResponseInterface } from '../SensorType/SensorTypeResponseInterface';
import { SensorReadingTypeResponseInterface } from '../ReadingTypes/SensorReadingTypeResponseInterfaces/SensorReadingTypeResponseInterface';

export default interface SensorResponseInterface {
    sensorID: number,
    sensorName: string,
    createdBy?: UserResponseInterface,
    device?: DeviceResponseInterface,
    sensorType?: SensorTypeResponseInterface,
    sensorReadingTypes?: SensorReadingTypeResponseInterface,
    canEdit?: boolean,
    canDelete?: boolean,
    userHasCardView?: boolean,
}