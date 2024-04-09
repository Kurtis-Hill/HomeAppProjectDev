import axios, { AxiosPromise } from 'axios';
import { apiURL } from '../../../Common/URLs/CommonURLs';

export async function getNewTriggerForm(): Promise<AxiosPromise> {
    const newTriggerFormRequest = await axios.get(
        `${apiURL}sensor-trigger/form/get`,
    );

    return newTriggerFormRequest;
}