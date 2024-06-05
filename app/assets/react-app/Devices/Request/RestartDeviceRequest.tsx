import axios, {AxiosResponse} from "axios";

import {apiURL} from "../../Common/URLs/CommonURLs";

export async function restartDeviceRequest(deviceID: number, responseType?: string): Promise<AxiosResponse> {
    return await axios.get(
        `${apiURL}user-devices/${deviceID}/restart?${responseType}`
    );
}
