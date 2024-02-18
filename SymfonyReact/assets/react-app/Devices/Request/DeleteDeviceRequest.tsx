import axios, { AxiosError, AxiosResponse } from 'axios';

import { apiURL } from "../../Common/URLs/CommonURLs";

export async function deleteDeviceRequest(deviceID: number, responseType?: string): Promise<AxiosResponse> {
    const deviceDeleteResponse: AxiosResponse = await axios.delete(
        `${apiURL}user-devices/${deviceID}/delete?${responseType}`
    )

    return deviceDeleteResponse;
}