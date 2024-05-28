import axios, { AxiosError, AxiosResponse } from 'axios';

import { apiURL } from '../../../Common/URLs/CommonURLs';

export async function getAllRoomRequest(): Promise<AxiosResponse> {
    const getAllRoomResponse: AxiosResponse = await axios.get(
        `${apiURL}user-rooms/all`,
    );

    return getAllRoomResponse;
}