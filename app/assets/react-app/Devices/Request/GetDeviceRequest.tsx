import axios, {AxiosResponse} from 'axios';
import {apiURL} from "../../Common/URLs/CommonURLs";
import {ResponseTypeEnum} from "../../Common/Response/APIResponseEnum";

export async function getDeviceRequest(deviceID: number, type: string|null): Promise<AxiosResponse> {
    return await axios.get(
        `${apiURL}user-device/${deviceID}`,
        {params: {responseType: type}}
    );
}

export async function getAllDevicesRequest(type: string|null): Promise<AxiosResponse> {
    return await axios.get(
        `${apiURL}user-devices`,
        {params: {responseType: type ?? ResponseTypeEnum.ResponseTypeFull}}
    );
}
