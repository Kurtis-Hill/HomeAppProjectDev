import axios, {AxiosResponse} from 'axios';

import { apiURL } from "../../Common/CommonURLs";
import { getAPIHeader } from "../../Common/APICommon";

export async function handleNavBarRequest(): Promise<AxiosResponse> {
    return await axios.get(
        `${apiURL}navbar/navbar-data`,
        getAPIHeader()
    );
}
