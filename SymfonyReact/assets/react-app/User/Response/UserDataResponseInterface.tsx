import GroupNameNavbarResponseInterface from "../../UserInterface/Navbar/Response/GroupNameNavbarResponseInterface";
import RoomNavbarResponseInterface from "../../UserInterface/Navbar/Response/RoomNavbarResponseInterface";

export interface UserDataResponseInterface {
    userRooms: RoomNavbarResponseInterface[];
    userGroups: GroupNameNavbarResponseInterface[];
}