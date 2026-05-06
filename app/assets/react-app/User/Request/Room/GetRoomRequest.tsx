import axios, {  AxiosResponse } from 'axios';

import { apiURL } from '../../../Common/URLs/CommonURLs';

export async function getRoomRequest(roomID: number): Promise<AxiosResponse> {
    return await axios.get(
        `${apiURL}user-rooms/${roomID}`,
    );
}
