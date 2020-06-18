export const getUser = () => {
    const useStr = sessionStorage.getItem('user');
    if (userStr) return JSON.parse(useStr);
    else return null;
}

export const getToken = () => {
    return sessionStorage.getItem('token') || null;
}

export const removeUserSession = () => {
    sessionStorage.removeItem('token');
    sessionStorage.removeItem('user');
    
}

export const setUserSession = (token, user) => {
    sessionStorage.setItem('token' , token);
    sessionStorage.setItem('user' , JSON.stringify(user));
}

