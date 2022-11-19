import axios from 'axios';

import { baseApiURL } from "../Common/CommonURLs";
import { removeUserSession } from "../Session/UserSession";

export async function handlePingRequest(): Promise<PingInterface> {
    const pingResponse: PingInterface = await axios.get(
        `${baseApiURL}ping`,
    );
    // removeUserSession();

    return pingResponse;
}

export interface PingInterface {
    data: string;
    status: number;
}
