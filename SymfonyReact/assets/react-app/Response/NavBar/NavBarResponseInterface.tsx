import DeviceInterface from '../User/Interfaces/DevicesInterface';
import RoomInterface from '../User/Interfaces/DevicesInterface';
import GroupNameInterface from '../User/Interfaces/GroupNameInterface';

export default interface NavBarResponseInterface {
    userRooms: RoomInterface;
    devices: DeviceInterface;
    groupNames: GroupNameInterface;
    errors: Array<string>
}