import axios, {AxiosPromise} from 'axios';

import {apiURL} from "../../../Common/URLs/CommonURLs";
import {ResponseTypeEnum} from "../../../Common/Response/APIResponseEnum";

export async function getAllSensorTriggerTypesRequest(sensorID?: number): Promise<AxiosPromise> {
    const params: Record<string, number> = {};
    if (sensorID !== undefined && sensorID !== null) {
        params['sensorID'] = sensorID;
    }

    return await axios.get(
        `${apiURL}sensor-trigger?responseType=${ResponseTypeEnum.ResponseTypeFull}`,
        { params }
    );
}
