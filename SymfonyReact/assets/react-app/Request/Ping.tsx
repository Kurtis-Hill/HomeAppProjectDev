import axios from 'axios';

import { baseApiURL } from "../Common/CommonURLs";

import { getAPIHeader } from "../Common/APICommon";

import { LoginResponseInterface } from "../Response/Login/Interfaces/LoginResponseInterface";
import { TokenRefreshResponseInterface } from "../Response/Token/Interfaces/TokenRefreshResponseInterface";

import { LoginFormUserInputsInterface } from "../Components/Form/UserInputs/Interface/LoginFormUserInputsInterface"

import { getRefreshToken } from "../session/UserSession"


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
