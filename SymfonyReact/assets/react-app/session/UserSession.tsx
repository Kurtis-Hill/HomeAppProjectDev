import { LoginResponseInterface } from "../Response/Login/Interfaces/LoginResponseInterface";

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

export const getRefreshToken = (): string|null => {
    return localStorage.getItem('refreshToken');
}

export const getRoles = (): Array<string>|null => {
    return JSON.parse(localStorage.getItem('roles')) || null;
}

export const checkAdmin = (): boolean => {
    const roles = getRoles();

    for(let i = 0; i < roles.length; ++i) {
        if (roles[i].match('ROLE_ADMIN')) {
            return true;
        }
        if (i === roles.length) {
            return false;
        }
    }
}
