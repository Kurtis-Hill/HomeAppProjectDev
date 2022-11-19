import axios from 'axios';

import { baseApiURL } from "../Common/CommonURLs";

import { getAPIHeader } from "../Common/APICommon";

export async function handlePingRequest(): Promise<PingInterface> {
    const pingRequest = await axios.get(
        `${baseApiURL}ping`,
        getAPIHeader()
    );        

    return pingRequest;
}

export interface PingInterface {
    data: string;
    status: number;
}
