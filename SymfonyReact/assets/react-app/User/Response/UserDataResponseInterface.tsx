import GroupNameResponseInterface from "./GroupName/GroupNameResponseInterface";
import RoomNavbarResponseInterface from "../../UserInterface/Navbar/Response/RoomNavbarResponseInterface";

export interface UserDataResponseInterface {
    userRooms: RoomNavbarResponseInterface[];
    userGroups: GroupNameResponseInterface[];
}