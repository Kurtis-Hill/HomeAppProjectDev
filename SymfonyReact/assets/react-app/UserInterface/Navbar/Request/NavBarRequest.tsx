import axios, {AxiosResponse} from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";

export async function handleNavBarRequest(): Promise<AxiosResponse> {
    const navbarResponse =  await axios.get(
        `${apiURL}navbar/navbar-data`,
    );

    if (navbarResponse.status === 200) {
        return navbarResponse;
    } else {
        throw new Error('Something went wrong getting navbar data');
    }
}
