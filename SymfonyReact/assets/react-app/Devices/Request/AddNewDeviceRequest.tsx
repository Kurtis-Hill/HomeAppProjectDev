import axios, { AxiosError, AxiosResponse } from 'axios';

import { apiURL } from "../../Common/URLs/CommonURLs";

export async function addNewDeviceRequest(addNewDeviceInputs: AddNewDeviceInputInterface): Promise<AxiosResponse> {
    try {
        const addNewDeviceResponse: AxiosResponse = await axios.post(
            `${apiURL}user-devices/add`,
            addNewDeviceInputs
        );

        if (addNewDeviceResponse.status === 200) {
            return addNewDeviceResponse;
        }

        throw Error('Error in addNewDeviceRequest');
    } catch (err) {
        const error = err as Error | AxiosError;
        Promise.reject()
    }

    return null;
}   

export interface AddNewDeviceInputInterface {
    'deviceName': string
    'devicePassword': string
    'devicePasswordCheck': string
    'deviceRoom': number
    'deviceGroup': number
}
