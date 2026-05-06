import GroupResponseInterface from "../../../User/Response/Group/GroupResponseInterface";
import RoomNavbarResponseInterface from "./RoomNavbarResponseInterface";

export interface UserDataResponseInterface {
    userRooms: RoomNavbarResponseInterface[];
    userGroups: GroupResponseInterface[];
}