export const getToken = (): string|null => {
    return localStorage.getItem('token') || null;
}

export const getRefreshToken = (): string|null => {
    return localStorage.getItem('refreshToken') || null;
}
