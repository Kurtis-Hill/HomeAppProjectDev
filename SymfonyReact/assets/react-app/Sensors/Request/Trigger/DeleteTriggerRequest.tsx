import axios, { AxiosPromise } from 'axios';
import { apiURL } from '../../../Common/URLs/CommonURLs';

export async function deleteTriggerRequest(triggerID: number): Promise<AxiosPromise> {
    const deleteTriggerRequest = await axios.delete(
        `${apiURL}sensor-trigger/${triggerID}/delete`,
    );

    return deleteTriggerRequest;
}