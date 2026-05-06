import axios, {AxiosResponse} from 'axios';

import {apiURL} from "../../../Common/URLs/CommonURLs";
import {ResponseTypeEnum} from "../../../Common/Response/APIResponseEnum";

export async function addNewSensorRequest(newSensorData: NewSensorInterface): Promise<AxiosResponse> {
    return await axios.post(
        `${apiURL}sensor?responseType=${ResponseTypeEnum.ResponseTypeFull}`,
        newSensorData,
    );
}

export interface NewSensorInterface {
    sensorName: string,
    deviceID: number,
    sensorTypeID: number,
}
