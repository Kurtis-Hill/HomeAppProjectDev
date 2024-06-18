import axios, {  AxiosResponse } from 'axios';

import { apiURL } from '../../../Common/URLs/CommonURLs';

export async function getAllRoomRequest(): Promise<AxiosResponse> {
    return await axios.get(
        `${apiURL}user-rooms/all`,
    );
}