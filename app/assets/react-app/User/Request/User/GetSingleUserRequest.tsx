import axios, { AxiosResponse} from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";

export async function getSingleUserRequest(userID: number): Promise<AxiosResponse> {
    const getSingleUserResponse = await axios.get(
        `${apiURL}${userID}`
    );

    return getSingleUserResponse;
}
