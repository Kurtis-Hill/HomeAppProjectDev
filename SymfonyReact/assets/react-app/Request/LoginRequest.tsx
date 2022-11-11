import axios, {AxiosResponse} from 'axios';

import { apiURL } from "../Common/CommonURLs";
import { setUserSession, refreshUserTokens } from "../session/UserSession";

import { LoginResponseInterface } from "../Response/Login/Interfaces/LoginResponseInterface";
import { TokenRefreshResponseInterface } from "../Response/Token/Interfaces/TokenRefreshResponseInterface";

import { LoginFormUserInputsInterface } from "../Components/Form/UserInputs/Interface/LoginFormUserInputsInterface"

import { getRefreshToken } from "../session/UserSession"

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
    const refreshTokenResponse: AxiosResponse = await axios.post(
        `${apiURL}token/refresh`,
        { refreshToken : getRefreshToken() }
    )

    const refreshTokenResponseData: TokenRefreshResponseInterface = refreshTokenResponse.data;

    if (refreshTokenResponse.status === 200) {
        refreshUserTokens(refreshTokenResponseData);
    }

    return refreshTokenResponse;
} 
