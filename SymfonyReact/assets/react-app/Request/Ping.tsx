import axios from 'axios';

import { apiURL } from "../Common/CommonURLs";
import { getToken } from "../Common/APICommon";

export async function handlePingRequest(): Promise<PingInterface> {
    const token = getToken();
    const pingResponse: PingInterface = await axios.get(
        `${apiURL}ping`,
        { headers: {"Authorization" : `Bearer ${token}`} }
    );

    return pingResponse;
}

export interface PingInterface {
    data: string;
    status: number;
}
