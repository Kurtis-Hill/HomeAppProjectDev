import { LoginResponseInterface } from "../Response/Login/Interfaces/LoginResponseInterface";

import { TokenRefreshResponseInterface } from "../Response/Token/Interfaces/TokenRefreshResponseInterface";

export const setUserSession = (loginResponse: LoginResponseInterface): void => {
    localStorage.removeItem('token');
    localStorage.removeItem('refreshToken');
    localStorage.removeItem('userID');
    localStorage.removeItem('roles');

    localStorage.setItem('token' , loginResponse.token);
    localStorage.setItem('refreshToken' , loginResponse.refreshToken);
    localStorage.setItem('userID' , loginResponse.userData.userID.toString());
    localStorage.setItem('roles' , JSON.stringify(loginResponse.userData.roles));
}

export const removeUserSession = (): void => {
    localStorage.removeItem('token');
    localStorage.removeItem('refreshToken');
    localStorage.removeItem('userID');
    localStorage.removeItem('roles');
}

export const refreshUserTokens = (refreshTokenResponseData: TokenRefreshResponseInterface): void => {
    if (refreshTokenResponseData.token !== undefined && refreshTokenResponseData.refreshToken !== undefined) {
        localStorage.removeItem('token');
        localStorage.removeItem('refreshToken');

        localStorage.setItem('token' , refreshTokenResponseData.token);
        localStorage.setItem('refreshToken' , refreshTokenResponseData.refreshToken);
    } else {
        console.log("refresh token response data is undefined")
    }
}

export const getRefreshToken = (): string|null => {
    return localStorage.getItem('refreshToken');
}

export const getRoles = (): Array<string>|null => {
    return JSON.parse(localStorage.getItem('roles')) || null;
}

export const checkAdmin = (): boolean => {
    const roles: Array<string>|null = getRoles();

    if (roles !== null) {
        for(let i = 0; i < roles.length; ++i) {
            if (roles[i].match('ROLE_ADMIN')) {
                return true;
            }
            if (i === roles.length) {
                return false;
            }
        }
    }
}
