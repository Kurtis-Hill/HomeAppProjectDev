import axios, { AxiosResponse } from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";
import {ResponseTypeEnum} from "../../../Common/Response/APIResponseEnum";

export async function sensorReadingTypesRequest(type: string | null = ResponseTypeEnum.ResponseTypeFull): Promise<AxiosResponse> {
        return await axios.get(
            `${apiURL}reading-types`,
            { params: { responseType: type } }
        );
}
