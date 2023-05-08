import GroupResponseInterface from "./Group/GroupResponseInterface";

export default interface UserResponseInterface {
    userID: number,
    firstName: string,
    lastName: string,
    email: string,
    createdAt: Date,
    group?: GroupResponseInterface,
    profilePicture?: string,
    roles?: string[],
}