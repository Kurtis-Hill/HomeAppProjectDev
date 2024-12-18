import axios, {AxiosResponse} from 'axios';
import {apiURL} from "../../Common/URLs/CommonURLs";

export async function getDeviceRequest(deviceID: number, type: string|null): Promise<AxiosResponse> {
    return await axios.get(
        `${apiURL}user-device/${deviceID}`,
        {params: {responseType: type}}
    );
}

export async function getAllDevicesRequest(): Promise<AxiosResponse> {
    return await axios.get(
        `${apiURL}user-devices`,
    );
}
