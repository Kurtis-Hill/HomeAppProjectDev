import axios, { AxiosResponse } from 'axios';

import { apiURL } from "../../Common/URLs/CommonURLs";

export async function pingDeviceRequest(deviceID: number, responseType?: string): Promise<AxiosResponse> {
    const devicePingResponse: AxiosResponse = await axios.get(
        `${apiURL}user-devices/${deviceID}/ping?${responseType}`
    )

    return devicePingResponse;
}