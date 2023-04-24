import axios, {AxiosError, AxiosResponse} from 'axios';
import { NavigateFunction, useNavigate } from "react-router-dom";

import { apiURL, indexUrl } from '../../URLs/CommonURLs';
import { getRefreshToken } from '../../../Authentication/Tokens/GetAPITokens';
import { ErrorResponseInterface } from '../../Response/ErrorResponseInterface';
import { loginUrl } from '../../URLs/CommonURLs';
import { handleTokenRefresh } from '../../../Authentication/Request/LoginRequest';

export function ErrorResponseInterceptor(props: {showErrorAnnouncementFlash: (errors: Array<string>, title: string, timer?: number|null) => void}): void {
    const errorAnnouncementFlash = props.showErrorAnnouncementFlash;

    const navigate: NavigateFunction = useNavigate();

    axios.interceptors.response.use(function (response) {
        const errors: ErrorResponseInterface = response.data;
        if (
            response.status !== 200
            && response.status !== 201
            && response.status !== 202
            && response.status !== 204
            || errors?.errors !== undefined
        ) {
            let errorMessages: Array<string> = [];
            errors?.errors.forEach((error: string) => {
                errorMessages.push(error);
            });
            errorAnnouncementFlash(errorMessages, errors.title);
        } else {
        }

        return response;
    }, async function (error: AxiosError) {
        console.log('haha error', error.response.data);
        if (error.response.config.url ===  `${apiURL}token/refresh` && window.location.pathname !==`${loginUrl}`) {        
            window.location.replace(`${loginUrl}`)
        }
        
        if (typeof error.response.data === 'object' &&  "errors" in error.response.data) {
            const errorResponse: ErrorResponseInterface = error.response.data as ErrorResponseInterface;
            const errorsForModal: Array<string> = errorResponse.errors;

            errorAnnouncementFlash(errorsForModal, 'Error' ?? errorResponse.title );

        } else {
            if (error.response.status === 401 || error.response.status === 403) {
                const refreshToken: string|null = getRefreshToken();
                console.log('refresh token', refreshToken);
                if (refreshToken !== null) {
                    try {
                        const refreshTokenResponse: AxiosResponse = await handleTokenRefresh();
                    } catch (err) {
                        const error = err as Error | AxiosError;
                        alert('Your session has expired please log in again');
                    }
                } else {
                    window.location.replace(`${loginUrl}`)
                }
            } 
            if (error.response.status === 500) {
                errorAnnouncementFlash(['Unrecognized issue please log out and back in again'], 'Error');
            }
            if (error.response.status === 404) {
                navigate(`${indexUrl}`)
            }
        }
    });
}
