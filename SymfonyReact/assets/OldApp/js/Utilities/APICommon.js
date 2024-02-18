export const getToken = () => {
    return sessionStorage.getItem('token') || null;
}

export const getRefreshToken = () => {
    return sessionStorage.getItem('refreshToken') || null;
}

export const getAPIHeader = (extraHeader = null) => {
    return { headers: {"Authorization" : `Bearer ${getToken()}`} };
}
