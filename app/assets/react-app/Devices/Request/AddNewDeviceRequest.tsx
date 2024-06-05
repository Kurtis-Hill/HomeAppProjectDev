import axios, {AxiosResponse} from 'axios';

import {apiURL} from "../../Common/URLs/CommonURLs";
import {ResponseTypeEnum} from "../../Common/Response/APIResponseType";

export async function addNewDeviceRequest(addNewDeviceInputs: AddNewDeviceInputInterface): Promise<AxiosResponse> {
    return await axios.post(
        `${apiURL}user-devices/add?responseType=${ResponseTypeEnum.SensitiveFull}`,
        addNewDeviceInputs,
    );
}

export interface AddNewDeviceInputInterface {
    'deviceName': string
    'devicePassword': string
    'devicePasswordCheck': string
    'deviceRoom': number
    'deviceGroup': number
    'deviceIPAddress'?: string
}
