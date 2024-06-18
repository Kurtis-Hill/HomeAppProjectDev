import axios, {AxiosError, AxiosResponse} from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";
import { RequestTypeEnum } from '../../../Common/Request/RequestTypeEnum';

export type GetSensorsRequestType = {
    limit: number,
    page: number,
    deviceIDs: number[],
    deviceNames: string[],
    cardViewIDs: number[],
    responseType: RequestTypeEnum,
}

export async function getSensorsRequest(getSensorInputs: GetSensorsRequestType): Promise<AxiosResponse> {
    const getSensorsUrlSearchParams = new URLSearchParams();

    getSensorsUrlSearchParams.append('responseType', getSensorInputs.responseType);
    getSensorInputs.deviceIDs.map((deviceID: number) => {
        getSensorsUrlSearchParams.append('deviceIDs[]', deviceID.toString());
    })

    getSensorInputs.deviceNames.map((deviceName: string) => {
        getSensorsUrlSearchParams.append('deviceNames[]', deviceName);
    })

    getSensorInputs.cardViewIDs.map((cardViewID: number) => {
        getSensorsUrlSearchParams.append('cardViewIDs[]', cardViewID.toString());
    })

    getSensorsUrlSearchParams.append('limit', getSensorInputs.limit.toString());
    getSensorsUrlSearchParams.append('page', getSensorInputs.page.toString());

    const getSensorRequest = await axios.get(
        `${apiURL}sensors/all?${getSensorsUrlSearchParams.toString()}`,
    );

    return getSensorRequest;
}