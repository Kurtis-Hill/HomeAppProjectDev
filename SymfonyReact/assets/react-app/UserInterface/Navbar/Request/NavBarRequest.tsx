import axios, {AxiosResponse} from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";

export async function handleNavBarRequest(): Promise<AxiosResponse> {
    return await axios.get(
        `${apiURL}navbar/navbar-data`,
    );
}
