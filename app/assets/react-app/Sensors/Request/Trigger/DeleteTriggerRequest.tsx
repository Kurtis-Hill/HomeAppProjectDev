import axios, {AxiosPromise} from 'axios';
import {apiURL} from '../../../Common/URLs/CommonURLs';

export async function deleteTriggerRequest(triggerID: number): Promise<AxiosPromise> {
    return await axios.delete(
        `${apiURL}sensor-trigger/${triggerID}`,
    );
}
