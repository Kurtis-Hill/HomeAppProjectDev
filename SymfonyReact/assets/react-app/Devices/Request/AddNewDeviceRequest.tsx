import axios, { AxiosError, AxiosResponse } from 'axios';

import { apiURL } from "../../Common/URLs/CommonURLs";

export async function addNewDeviceRequest(addNewDeviceInputs: AddNewDeviceInputInterface): Promise<AxiosResponse> {
    const addNewDeviceResponse: AxiosResponse = await axios.post(
        `${apiURL}user-devices/add`,
        addNewDeviceInputs
    );

    return addNewDeviceResponse;
}   

export interface AddNewDeviceInputInterface {
    'deviceName': string
    'devicePassword': string
    'devicePasswordCheck': string
    'deviceRoom': number
    'deviceGroup': number
}
