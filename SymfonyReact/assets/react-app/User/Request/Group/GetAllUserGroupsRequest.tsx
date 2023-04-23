import * as React from 'react';

import axios, { AxiosError, AxiosResponse } from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";

export async function getAllUserGroupsRequest(responseType?: string) {
    const getAllUserGroupsResponse: AxiosResponse = await axios.get(
        `${apiURL}user-groups/all`,
        { params: { responseType: responseType } }
    );

    if (getAllUserGroupsResponse.status === 200) {
        return getAllUserGroupsResponse;
    } else {
        throw new Error('Something went wrong');
    }
}

export interface GroupResponseInterface {
    groupID: number
    groupName: string
}