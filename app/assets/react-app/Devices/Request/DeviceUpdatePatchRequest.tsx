import axios, {AxiosResponse} from 'axios';

import {apiURL} from "../../Common/URLs/CommonURLs";

export async function deviceUpdatePatchRequest(
    deviceID: number,
    deviceUpdatePatchInputs: DeviceUpdatePatchInputInterface,
    responseType?: string
): Promise<AxiosResponse> {
    return await axios.patch(
        `${apiURL}user-devices/${deviceID}`,
        deviceUpdatePatchInputs,
        {params: {responseType: responseType}}
    );
}

export interface DeviceUpdatePatchInputInterface {
    'deviceName'?: string;
    'password'?: string;
    'deviceGroup'?: number;
    'deviceRoom'?: number;
}
