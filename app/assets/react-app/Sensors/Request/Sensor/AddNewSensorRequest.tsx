import axios, {AxiosResponse} from 'axios';

import {apiURL} from "../../../Common/URLs/CommonURLs";

export async function addNewSensorRequest(newSensorData: NewSensorInterface): Promise<AxiosResponse> {
    return await axios.post(
        `${apiURL}sensor/add`,
        newSensorData,
    );
}

export interface NewSensorInterface {
    sensorName: string,
    deviceID: number,
    sensorTypeID: number,
}
