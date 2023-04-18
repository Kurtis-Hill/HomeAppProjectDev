import axios, { AxiosError, AxiosResponse } from 'axios';

import { apiURL } from "../../Common/URLs/CommonURLs";
import RoomResponseInterface from '../../User/Response/Room/RoomResponseInterface';
import GroupNameResponseInterface from '../../User/Response/GroupName/GroupNameResponseInterface';

export async function getDeviceRequest(deviceID: number, type: string|null): Promise<AxiosResponse> {
    const getDeviceResponse: AxiosResponse = await axios.get(
        `${apiURL}user-device/${deviceID}`,
        { params: { responseType: type } }  
    );

    if (getDeviceResponse.status === 200) {
        return getDeviceResponse;
    } else {
        throw new Error('Something went wrong');
    }
}

export interface DeviceResponseInterface {
    deviceID: number
    deviceName: string
    devicePassword: string
    deviceRoom: number
    deviceGroup: GroupNameResponseInterface
    roles: string[]
    room: RoomResponseInterface
    ipAddress: string|null
    externalIpAddress: string|null
    secret : string|null

}