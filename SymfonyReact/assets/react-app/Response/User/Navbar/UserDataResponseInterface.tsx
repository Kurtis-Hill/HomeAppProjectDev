import GroupNameNavbarResponseInterface from "./Interfaces/GroupNameNavbarResponseInterface";
import RoomNavbarResponseInterfaceInterface from "./Interfaces/RoomNavbarResponseInterface";

export interface UserDataResponseInterface {
    userRooms: RoomNavbarResponseInterfaceInterface[];
    userGroups: GroupNameNavbarResponseInterface[];
}