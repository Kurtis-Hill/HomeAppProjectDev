import axios, {AxiosError, AxiosResponse} from 'axios';

import { apiURL } from "../../../../Common/URLs/CommonURLs";

import { RequestTypeEnum } from "../../../../Common/API/RequestTypeEnum";

export async function getAllSensorTriggerTypesRequest(): Promise<GetTrigg> {
    const getSensorTriggerTypesRequest = await axios.get(
        `${apiURL}sensor-trigger/get/all`,
    );

    return getSensorTriggerTypesRequest;
}