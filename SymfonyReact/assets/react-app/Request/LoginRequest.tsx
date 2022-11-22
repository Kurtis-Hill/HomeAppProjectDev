import axios, {AxiosError, AxiosResponse} from 'axios';

import { apiURL } from "../Common/CommonURLs";
import { setUserSession, refreshUserTokens } from "../Session/UserSession";

import { LoginResponseInterface } from "../Response/Login/Interfaces/LoginResponseInterface";
import { TokenRefreshResponseInterface } from "../Response/Token/Interfaces/TokenRefreshResponseInterface";

import { LoginFormUserInputsInterface } from "../Components/Form/UserInputs/Interface/LoginFormUserInputsInterface"

import { getRefreshToken } from "../Session/UserSession"

export async function handleLogin(userInputs: LoginFormUserInputsInterface): Promise<AxiosResponse> {
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
    try {
        const refreshTokenResponse: AxiosResponse = await axios.post(
            `${apiURL}token/refresh`,
            { refreshToken : refreshToken }
        )

        const refreshTokenResponseData: TokenRefreshResponseInterface = refreshTokenResponse.data;

        if (refreshTokenResponse.status === 200) {
            refreshUserTokens(refreshTokenResponseData);
        }

        return refreshTokenResponse;
    } catch (err) {
        const error = err as Error | AxiosError;
    }
}
