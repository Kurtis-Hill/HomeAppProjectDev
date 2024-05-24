import axios, {AxiosError, AxiosResponse} from 'axios';

import { apiURL } from "../../Common/URLs/CommonURLs";
import { setUserSession, refreshUserTokens } from '../Session/UserSession';

import { LoginResponseInterface } from "../Response/LoginResponseInterface";
import { TokenRefreshResponseInterface } from "../Response/TokenRefreshResponseInterface";

import { LoginUserInputsInterface } from "../Form/LoginUserInputsInterface"

import { getRefreshToken } from '../Tokens/GetAPITokens'; 

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
        const refreshTokenResponse: AxiosResponse = await axios.post(
            `${apiURL}token/refresh`,
            { refreshToken : refreshToken }
        )

        return refreshTokenResponse;
    // } catch (err) {
    //     const error = err as Error | AxiosError;
    // }
}
