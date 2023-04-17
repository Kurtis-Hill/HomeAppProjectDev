import axios, { AxiosError, AxiosResponse } from 'axios';

import { apiURL } from "../../Common/URLs/CommonURLs";

export async function addNewDeviceRequest(addNewDeviceInputs: AddNewDeviceInputInterface): Promise<AxiosResponse> {
    const addNewDeviceResponse: AxiosResponse = await axios.post(
        `${apiURL}user-devices/add`,
        addNewDeviceInputs
    );

    if (addNewDeviceResponse.status === 201) {
        return addNewDeviceResponse;
    } else {
        throw new Error('Something went wrong');
    }
}   

export interface AddNewDeviceInputInterface {
    'deviceName': string
    'devicePassword': string
    'devicePasswordCheck': string
    'deviceRoom': number
    'deviceGroup': number
}
