import { LoginResponseInterface } from "../Response/LoginResponseInterface";

import { TokenRefreshResponseInterface } from "../Response/TokenRefreshResponseInterface";

export const tokenName = 'token';
export const refreshTokenName = 'refreshToken';
export const userIDName = 'userID';
export const rolesName = 'roles';

export const setUserSession = (loginResponse: LoginResponseInterface): void => {
    removeUserSession();

    localStorage.setItem(tokenName , loginResponse.token);
    localStorage.setItem(refreshTokenName , loginResponse.refreshToken);
    localStorage.setItem(userIDName , loginResponse.userData.userID.toString());
    localStorage.setItem(rolesName, JSON.stringify(loginResponse.userData.roles));
}

export const removeUserSession = (): void => {
    localStorage.removeItem(tokenName);
    localStorage.removeItem(refreshTokenName);
    localStorage.removeItem(userIDName);
    localStorage.removeItem(rolesName);
}

export const refreshUserTokens = (refreshTokenResponseData: TokenRefreshResponseInterface): void => {
    if (refreshTokenResponseData.token !== undefined && refreshTokenResponseData.refreshToken !== undefined) {
        localStorage.removeItem(tokenName);
        localStorage.removeItem(refreshTokenName);

        localStorage.setItem(tokenName , refreshTokenResponseData.token);
        localStorage.setItem(refreshTokenName , refreshTokenResponseData.refreshToken);
    } else {
        throw Error('Token or Refresh Token is undefined');
    }
}

export const getUserSessionValue = (key: string): string => {
    return localStorage.getItem(key);
}

export const getRoles = (): Array<string>|null => {
    return JSON.parse(localStorage.getItem(rolesName)) || null;
}

export const checkAdmin = (): boolean => {
    const roles: Array<string>|null = getRoles();

    if (roles !== null) {
        return roles.includes('ROLE_ADMIN');
    }
}

export const getToken = (): string|null => {
    return localStorage.getItem(tokenName) || null;
}

export const getRefreshToken = (): string|null => {
    return localStorage.getItem(refreshTokenName) || null;
}

export const removeTokens = (): void => {
    localStorage.removeItem(tokenName);
    localStorage.removeItem(refreshTokenName);
}
