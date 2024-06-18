import axios, {AxiosResponse} from 'axios';

import {apiURL} from "../../Common/URLs/CommonURLs";

export async function deleteDeviceRequest(deviceID: number, responseType?: string): Promise<AxiosResponse> {
    return await axios.delete(
        `${apiURL}user-devices/${deviceID}/delete?${responseType}`
    );
}
