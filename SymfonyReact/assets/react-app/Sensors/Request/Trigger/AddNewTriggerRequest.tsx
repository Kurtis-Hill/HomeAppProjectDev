import axios, {AxiosPromise} from 'axios';
import {apiURL} from '../../../Common/URLs/CommonURLs';
import {DaysEnum} from '../../../Common/DaysEnum';

export type AddNewTriggerType = {
    operator: number;
    triggerType: number;
    baseReadingTypeThatTriggers: number;
    baseReadingTypeThatIsTriggered: number;
    days: DaysEnum[];
    valueThatTriggers: number|boolean;
    startTime: number|null;
    endTime: number|null;
};

export async function addNewTriggerForm(triggerData: AddNewTriggerType): Promise<AxiosPromise> {
    return await axios.post(
        `${apiURL}sensor-trigger/form/add`,
        triggerData,
    );
}
