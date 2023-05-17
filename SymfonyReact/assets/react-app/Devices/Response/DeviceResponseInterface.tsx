import GroupResponseInterface from "../../User/Response/Group/GroupResponseInterface";
import RoomResponseInterface from "../../User/Response/Room/RoomResponseInterface";
import SensorResponseInterface from '../../Sensors/Sensor/Response/SensorResponseInterface';

export interface DeviceResponseInterface {
    deviceID?: number,
    deviceName: string,
    ipAddress: string|null,
    externalIpAddress: string|null,
    group?: GroupResponseInterface,
    room?: RoomResponseInterface,
    createdBy?: string|number,
    secret?: string|null,
    roles?: string[],
    sensorData?: SensorResponseInterface,
    canEdit?: boolean,
    canDelete?: boolean,
}
