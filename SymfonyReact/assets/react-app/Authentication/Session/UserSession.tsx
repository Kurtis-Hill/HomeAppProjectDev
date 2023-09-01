import { LoginResponseInterface } from "../Response/LoginResponseInterface";

import { TokenRefreshResponseInterface } from "../Response/TokenRefreshResponseInterface";

export const setUserSession = (loginResponse: LoginResponseInterface): void => {
    removeUserSession();

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
        throw Error('Token or Refresh Token is undefined');
    }
}

export const getUserSessionValue = (key: string): string => {
    return localStorage.getItem(key);
}

export const getRoles = (): Array<string>|null => {
    return JSON.parse(localStorage.getItem('roles')) || null;
}

export const checkAdmin = (): boolean => {
    const roles: Array<string>|null = getRoles();

    if (roles !== null) {
        return roles.includes('ROLE_ADMIN');
        // for(let i = 0; i < roles.length; ++i) {
        //     if (roles[i].match('ROLE_ADMIN')) {
        //         return true;
        //     }
        //     if (i === roles.length) {
        //         return false;
        //     }
        // }
    }
}
