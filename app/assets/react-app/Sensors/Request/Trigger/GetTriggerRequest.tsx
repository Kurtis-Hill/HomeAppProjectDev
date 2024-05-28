import axios, {AxiosPromise} from 'axios';

import {apiURL} from "../../../Common/URLs/CommonURLs";

export async function getSensorTriggerTypesRequest(triggerID: number): Promise<AxiosPromise> {
    return await axios.get(
        `${apiURL}sensor-trigger/${triggerID}/get`,
    );
}