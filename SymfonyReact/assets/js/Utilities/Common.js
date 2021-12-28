export const getUser = () => {
    const useStr = sessionStorage.getItem('user');
    if (userStr) return JSON.parse(useStr);
    else return null;
}

export const removeUserSession = () => {
    sessionStorage.removeItem('token');
    sessionStorage.removeItem('refreshToken');   
    sessionStorage.removeItem('userID');   
    sessionStorage.removeItem('roles');    
        
    return null;
}


export const setUserSession = (token, refreshToken, userData) => {
    sessionStorage.removeItem('token');
    sessionStorage.removeItem('refreshToken');
    sessionStorage.removeItem('userID');   
    sessionStorage.removeItem('roles');

    sessionStorage.setItem('token' , token);
    sessionStorage.setItem('refreshToken' , refreshToken);
    sessionStorage.setItem('userID' , userData.userID);
    sessionStorage.setItem('roles' , userData.roles);
}

export const capitalizeFirstLetter = (string) => {
    if (string != undefined) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    return null;
}

export const lowercaseFirstLetter = (string) => {
    if (string != undefined) {
        return string.charAt(0).toLowerCase() + string.slice(1);
    }
    return null;
}