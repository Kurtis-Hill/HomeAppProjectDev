import axios, {AxiosError, AxiosResponse} from 'axios';

import { baseApiURL } from "../../Common/URLs/CommonURLs";

export async function userDataRequest(): Promise<AxiosResponse> {
    try {
        const userDataResponse: AxiosResponse = await axios.get(
            `${baseApiURL}user-data/get`
        )

        return userDataResponse;
    } catch (err) {
        const error = err as Error | AxiosError;
       
        return Promise.reject();
    }
}
