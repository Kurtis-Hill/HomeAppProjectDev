import axios, {AxiosResponse} from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";
import {ResponseTypeEnum} from "../../../Common/Response/APIResponseEnum";

export async function sensorTypesRequest(): Promise<AxiosResponse> {
    return await axios.get(
        `${apiURL}sensor-types?responseType=${ResponseTypeEnum.ResponseTypeFull}`
    );
}
