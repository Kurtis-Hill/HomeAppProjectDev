import axios, {AxiosError, AxiosPromise, AxiosResponse} from 'axios';

import { baseApiURL } from "../../../Common/URLs/CommonURLs";

export interface SwitchSensorRequestInterface {
    sensorData: [
        {
            'sensorName': string,
            'currentReadings': 
                {
                    'relay': boolean,
                }
            ,
        }
    ]
}


export async function switchSensorRequest(sensorData: SwitchSensorRequestInterface): Promise<AxiosPromise> {
    return axios.post(
        `${baseApiURL}device/switch-sensor`,
        sensorData,
    );
}
