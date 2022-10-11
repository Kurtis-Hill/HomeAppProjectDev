import axios from 'axios';

import { apiURL } from "../Common/CommonURLs";

import { LoginResponseInterface } from "../Response/Login/Interfaces/LoginResponseInterface";
import { TokenRefreshResponseInterface } from "../Response/Token/Interfaces/TokenRefreshResponseInterface";

import { LoginFormUserInputs } from "../Components/Form/UserInputs/Interface/LoginFormUserInputs"

import { getRefreshToken } from "../session/UserSession"

export async function handleLogin(userInputs: LoginFormUserInputs): Promise<LoginResponseInterface> {
    const loginCheckResponse = await axios.post(
        `${apiURL}login_check`,
        userInputs
    );

    return loginCheckResponse.data
}

export async function handleTokenRefresh(): Promise<TokenRefreshResponseInterface> {
    const refreshTokenResponse = await axios.post(
        `${apiURL}token/refresh`,
        { refreshToken : getRefreshToken() }
    )

    return refreshTokenResponse.data;
} 
