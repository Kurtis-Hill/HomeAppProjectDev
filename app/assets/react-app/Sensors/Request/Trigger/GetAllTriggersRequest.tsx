import axios, {AxiosPromise} from 'axios';

import {apiURL} from "../../../Common/URLs/CommonURLs";

export async function getAllSensorTriggerTypesRequest(): Promise<AxiosPromise> {
    return await axios.get(
        `${apiURL}sensor-trigger/all`,
    );
}
