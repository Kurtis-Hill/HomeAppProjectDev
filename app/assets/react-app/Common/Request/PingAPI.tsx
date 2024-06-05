import axios from 'axios';

import {apiURL} from "../URLs/CommonURLs";
import {getToken} from "../../Authentication/Session/UserSessionHelper";

export async function handlePingRequest(): Promise<PingInterface> {
    const token = getToken();
    return await axios.get(
        `${apiURL}ping`,
        {"headers": {"Authorization": `Bearer ${token}`}}
    );
}

export interface PingInterface {
    data: string;
    status: number;
}
