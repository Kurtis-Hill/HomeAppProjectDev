import axios, { AxiosError, AxiosResponse } from 'axios';

import { apiURL } from "../../Common/URLs/CommonURLs";

export async function deviceUpdatePatchRequest(deviceID: number, deviceUpdatePatchInputs: DeviceUpdatePatchInputInterface, responseType?: string): Promise<AxiosResponse> {
    const deviceUpdatePatchResponse: AxiosResponse = await axios.patch(
        `${apiURL}user-devices/${deviceID}/update`,
        deviceUpdatePatchInputs,
        { params: { responseType: responseType } }
    );

    return deviceUpdatePatchResponse;
}

export interface DeviceUpdatePatchInputInterface {
    'deviceName'?: string;
    'password'?: string;
    'deviceGroup'?: number;
    'deviceRoom'?: number;
}