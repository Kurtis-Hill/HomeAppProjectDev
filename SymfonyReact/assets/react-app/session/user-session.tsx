import {LoginResponseInterface} from "../Response/Login/Interfaces/LoginResponseInterface";

export const setUserSession = (loginResponse: LoginResponseInterface): void => {
    localStorage.removeItem('token');
    localStorage.removeItem('refreshToken');
    localStorage.removeItem('userID');
    localStorage.removeItem('roles');

    localStorage.setItem('token' , loginResponse.token);
    localStorage.setItem('refreshToken' , loginResponse.refreshToken);
    localStorage.setItem('userID' , loginResponse.userData.userID.toString());
    localStorage.setItem('roles' , loginResponse.userData.roles.toString());
}

export const getRefreshToken = (): string|null => {
    return sessionStorage.getItem('refreshToken');
}