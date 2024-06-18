import GroupResponseInterface from "../Group/GroupResponseInterface";
import UserResponseInterface from "../UserResponseInterface";

export default interface GroupMappingResponseInterface {
    groupMappingID: number,
    user: UserResponseInterface,
    group: GroupResponseInterface,
}