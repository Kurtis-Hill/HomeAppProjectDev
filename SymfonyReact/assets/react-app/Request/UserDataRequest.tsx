import axios, {AxiosError, AxiosResponse} from 'axios';

import { apiURL } from "../Common/CommonURLs";

export async function userDataRequest(): Promise<AxiosResponse> {
    try {
        const userDataResponse: AxiosResponse = await axios.get(
            `${apiURL}user-data/get`
        )

        return userDataResponse;
    } catch (err) {
        const error = err as Error | AxiosError;
       
        return Promise.reject();
    }
}