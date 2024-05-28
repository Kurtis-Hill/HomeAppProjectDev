import axios, { AxiosError, AxiosResponse } from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";
import { ResponseTypeEnum, ResponseTypeFull } from '../../../Common/API/APIResponseType';

export type UserUpdateRequestType = {
    firstName?: string,
    lastName?: string,
    email?: string,
    groupID?: number,
    profilePicture?: string,
    roles?: string[],
    newPassword?: string,
    oldPassword?: string,
}

export default async function UserUpdateRequest(userData: UserUpdateRequestType, userID: number, responseType?: string): Promise<AxiosResponse> {
    const userUpdatePatchRequestResponse: AxiosResponse = await axios.patch(
        `${apiURL}${userID}/update`,
        userData,
        { params: { responseType: responseType ?? ResponseTypeFull } }
    );

    return userUpdatePatchRequestResponse;
}