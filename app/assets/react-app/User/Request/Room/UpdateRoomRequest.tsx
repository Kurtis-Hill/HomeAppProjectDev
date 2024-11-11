import axios, {AxiosResponse} from 'axios';

import {apiURL} from "../../../Common/URLs/CommonURLs";
import { ResponseTypeEnum } from '../../../Common/Response/APIResponseEnum';

export type UpdateRoomRequestType = {
    roomName?: string,
}

export default async function updateRoomRequest(roomID: number, roomData: UpdateRoomRequestType, responseType?: string): Promise<AxiosResponse> {
    return await axios.patch(
        `${apiURL}user-rooms/${roomID}`,
        roomData,
        {params: {responseType: responseType ?? ResponseTypeEnum.ResponseTypeFull}}
    );
}
