import axios, {AxiosResponse} from 'axios';

import {apiURL} from "../../Common/URLs/CommonURLs";
import {ResponseTypeEnum} from "../../Common/Response/APIResponseEnum";

export async function deleteDeviceRequest(deviceID: number, responseType = ResponseTypeEnum.ResponseTypeFull): Promise<AxiosResponse> {
    return await axios.delete(
        `${apiURL}user-devices/${deviceID}?responseType=${responseType}`
    );
}
