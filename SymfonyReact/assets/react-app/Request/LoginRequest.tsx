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
    console.log('handle token refresh....')
    const refreshToken: string = getRefreshToken();
    console.log('refresh token', refreshToken)
    try {
        const refreshTokenResponse: AxiosResponse = await axios.post(
            `${apiURL}token/refresh`,
            { refreshToken : refreshToken }
        )

        console.log('refresh token response', refreshTokenResponse)
        const refreshTokenResponseData: TokenRefreshResponseInterface = refreshTokenResponse.data;

        if (refreshTokenResponse.status === 200) {
            console.log('refresh token response data', refreshTokenResponseData.token, refreshTokenResponseData.refreshToken)
            refreshUserTokens(refreshTokenResponseData);
        }

        return refreshTokenResponse;
    } catch (err) {
        const error = err as Error | AxiosError;
        console.log('error of refresh', error)
    }
}
