import GroupNameNavbarResponseInterface from "./Interfaces/GroupNameNavbarResponseInterface";
import RoomNavbarResponseInterface from "./Interfaces/RoomNavbarResponseInterface";

export interface UserDataResponseInterface {
    userRooms: RoomNavbarResponseInterface[];
    userGroups: GroupNameNavbarResponseInterface[];
}