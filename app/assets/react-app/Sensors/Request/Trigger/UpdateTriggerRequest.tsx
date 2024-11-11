import axios, {AxiosPromise} from 'axios';
import {apiURL} from '../../../Common/URLs/CommonURLs';
import { AddNewTriggerType } from './AddNewTriggerRequest';

export async function updateTriggerData(triggerID: number, triggerData: AddNewTriggerType): Promise<AxiosPromise> {
    return await axios.put(
        `${apiURL}sensor-trigger/${triggerID}`,
        triggerData,
    );
}
