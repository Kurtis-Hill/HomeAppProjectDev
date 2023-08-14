import axios, { AxiosError, AxiosResponse } from 'axios';

import { apiURL } from "../../Common/URLs/CommonURLs";
import { SensitiveFull } from '../../Common/API/APIResponseType';

export async function addNewDeviceRequest(addNewDeviceInputs: AddNewDeviceInputInterface): Promise<AxiosResponse> {
    const addNewDeviceResponse: AxiosResponse = await axios.post(
        `${apiURL}user-devices/add?responseType=${SensitiveFull}`,
        addNewDeviceInputs,
    );

    return addNewDeviceResponse;
}   

export interface AddNewDeviceInputInterface {
    'deviceName': string
    'devicePassword': string
    'devicePasswordCheck': string
    'deviceRoom': number
    'deviceGroup': number
    'deviceIPAddress'?: string
}
