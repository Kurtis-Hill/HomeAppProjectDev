import DeviceInterface from '../User/Interfaces/DevicesInterface';
import RoomInterface from '../User/Interfaces/RoomInterface';
import GroupNameInterface from '../User/Interfaces/GroupNameInterface';

export default interface NavBarResponseInterface {
    userRooms: Array<RoomInterface>;
    devices: Array<DeviceInterface>;
    groupNames: Array<GroupNameInterface>;
    errors: Array<string>
}