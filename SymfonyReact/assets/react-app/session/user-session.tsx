import {LoginResponseInterface} from "../Response/Login/Interfaces/LoginResponseInterface";

export const setUserSession = (loginResponse: LoginResponseInterface) => {
    localStorage.removeItem('token');
    localStorage.removeItem('refreshToken');
    localStorage.removeItem('userID');
    localStorage.removeItem('roles');

    localStorage.setItem('token' , loginResponse.token);
    localStorage.setItem('refreshToken' , loginResponse.refreshToken);
    localStorage.setItem('userID' , loginResponse.userData.userID);
    localStorage.setItem('roles' , loginResponse.userData.roles);
}
