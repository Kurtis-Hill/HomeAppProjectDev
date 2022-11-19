import DeviceNavbarResponseInterface from '../User/Navbar/Interfaces/DeviceNavbarResponseInterface';
import RoomNavbarResponseInterfaceInterface from '../User/Navbar/Interfaces/RoomNavbarResponseInterface';
import GroupNameNavbarResponseInterface from '../User/Navbar/Interfaces/GroupNameNavbarResponseInterface';

export default interface NavBarResponseInterface {
    userRooms: Array<RoomNavbarResponseInterfaceInterface>;
    devices: Array<DeviceNavbarResponseInterface>;
    groupNames: Array<GroupNameNavbarResponseInterface>;
    errors: Array<string>
}