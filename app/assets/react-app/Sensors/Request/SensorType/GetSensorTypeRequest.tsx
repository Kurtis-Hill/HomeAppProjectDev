import axios, {AxiosError, AxiosResponse} from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";

export async function sensorTypesRequest(): Promise<AxiosResponse> {
    return await axios.get(
        `${apiURL}sensor-types/all`
    );
}