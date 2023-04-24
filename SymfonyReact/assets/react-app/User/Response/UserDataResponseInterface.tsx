import GroupResponseInterface from "./Group/GroupResponseInterface";
import RoomNavbarResponseInterface from "../../UserInterface/Navbar/Response/RoomNavbarResponseInterface";

export interface UserDataResponseInterface {
    userRooms: RoomNavbarResponseInterface[];
    userGroups: GroupResponseInterface[];
}