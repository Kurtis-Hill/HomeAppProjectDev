import axios, { AxiosError, AxiosResponse } from 'axios';

import { apiURL } from "../../Common/CommonURLs";

export async function addNewDeviceRequest(addNewDeviceInputs: AddNewDeviceInputInterface): Promise<AxiosResponse> {
    try {
        const addNewDeviceResponse: AxiosResponse = await axios.post(
            `${apiURL}user-devices/add`,
            addNewDeviceInputs
        );

        console.log('here is response', addNewDeviceResponse.status, addNewDeviceResponse)
        if (addNewDeviceResponse.status === 200) {
            console.log('here is response11', addNewDeviceResponse.status, addNewDeviceResponse)
            return addNewDeviceResponse;
        } else {
            throw Error('Error in addNewDeviceRequest');
        }
    } catch (err) {
        const error = err as Error | AxiosError;
        // Promise.reject()
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
