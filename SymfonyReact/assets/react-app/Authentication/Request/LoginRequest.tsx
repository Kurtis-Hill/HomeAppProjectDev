import axios, {AxiosResponse} from 'axios';

import {apiURL} from "../../Common/URLs/CommonURLs";
import {setUserSession} from '../Session/UserSession';

import {LoginResponseInterface} from "../Response/LoginResponseInterface";

import {LoginUserInputsInterface} from "../../Routes/Login/Login"

import {getRefreshToken} from '../Tokens/APITokenHandler';

export async function handleLogin(userInputs: LoginUserInputsInterface): Promise<AxiosResponse> {
    const loginCheckResponse: AxiosResponse = await axios.post(
        `${apiURL}login_check`,
        userInputs
    );

    const loginResponseData: LoginResponseInterface = loginCheckResponse.data;

    if (loginCheckResponse.status === 200) {
        setUserSession(loginResponseData);
    }

    return loginCheckResponse;
}

export async function handleTokenRefresh(): Promise<AxiosResponse> {
    const refreshToken: string = getRefreshToken();
    // try {
    return await axios.post(
            `${apiURL}token/refresh`,
            {refreshToken: refreshToken}
        );
    // } catch (err) {
    //     const error = err as Error | AxiosError;
    // }
}
