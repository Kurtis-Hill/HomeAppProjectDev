import axios, {AxiosError, AxiosResponse} from 'axios';

import { apiURL } from "../Common/CommonURLs";
import { setUserSession, refreshUserTokens } from "../Session/UserSession";

export async function handleSensorDatarequest(): Promise<AxiosResponse> {
    const sensorDataResponse: AxiosResponse = await axios.get(
        `${apiURL}sensor-data`
    );

    return sensorDataResponse;
}