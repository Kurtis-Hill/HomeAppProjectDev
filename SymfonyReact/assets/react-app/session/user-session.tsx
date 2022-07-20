export const setUserSession = (token, refreshToken, userData) => {
    localStorage.removeItem('token');
    localStorage.removeItem('refreshToken');
    localStorage.removeItem('userID');
    localStorage.removeItem('roles');

    localStorage.setItem('token' , token);
    localStorage.setItem('refreshToken' , refreshToken);
    localStorage.setItem('userID' , userData.userID);
    localStorage.setItem('roles' , userData.roles);
}
