import axios, { AxiosError, AxiosResponse } from 'axios';

import { apiURL } from "../../Common/URLs/CommonURLs";

export async function getDeviceRequest(deviceID: number): Promise<AxiosResponse> {
    const getDeviceResponse: AxiosResponse = await axios.get(
        `${apiURL}user-device/${deviceID}`
    );

    if (getDeviceResponse.status === 200) {
        return getDeviceResponse;
    } else {
        throw new Error('Something went wrong');
    }
}

export interface DeviceResponseInterface {
    'deviceID': number
    'deviceName': string
    'devicePassword': string
    'deviceRoom': number
    'deviceGroup': number
}