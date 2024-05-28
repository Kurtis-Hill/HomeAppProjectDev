import GroupResponseInterface from "./Group/GroupResponseInterface";

export default interface UserResponseInterface {
    userID: number,
    firstName: string,
    lastName: string,
    email: string,
    createdAt: Date,
    groups?: GroupResponseInterface,
    profilePicture?: string,
    roles?: string[],
    canEdit?: boolean,
    canDelete?: boolean,
}
