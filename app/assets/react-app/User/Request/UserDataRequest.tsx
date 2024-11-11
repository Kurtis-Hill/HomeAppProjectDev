import axios, {AxiosError, AxiosResponse} from 'axios';

import { baseApiURL } from "../../Common/URLs/CommonURLs";

export async function userDataRequest(): Promise<AxiosResponse> {
    return await axios.get(
        `${baseApiURL}user-data`
    );
}
