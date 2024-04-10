import axios, {AxiosPromise} from 'axios';
import {apiURL} from '../../../Common/URLs/CommonURLs';

export async function getNewTriggerForm(): AxiosPromise {
    return await axios.get(
        `${apiURL}sensor-trigger/form/get`,
    );
}
