import axios, { AxiosPromise } from 'axios';
import { apiURL } from '../../../Common/URLs/CommonURLs';
import { SensorDaysType as SensorTriggerDaysType } from '../../Types/SensorTriggerDaysType';
import { DaysEnum } from '../../../Common/DaysEnum';

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

export async function addNewTriggerForm(triggerData: AddNewTriggerType): AxiosPromise {
    const addNewTriggerRequest = await axios.post(
        `${apiURL}sensor-trigger/form/add`,
        triggerData,
    );

    return addNewTriggerRequest;
}
