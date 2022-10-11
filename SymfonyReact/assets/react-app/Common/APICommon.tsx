export const getAPIHeader = (extraHeader = null) => {
    return { headers: {"Authorization" : `Bearer ${getToken()}`} };
}

export const getToken = () => {
    return localStorage.getItem('token') || null;
}

export const getRefreshToken = () => {
    return localStorage.getItem('refreshToken') || null;
}