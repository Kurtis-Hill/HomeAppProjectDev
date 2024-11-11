import axios, { AxiosResponse } from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";

export async function sensorReadingTypesRequest(): Promise<AxiosResponse> {
        return await axios.get(
            `${apiURL}reading-types`
        );
}
