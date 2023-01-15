import axios, {AxiosError, AxiosResponse} from 'axios';

import { getRefreshToken } from "../../Common/APICommon";

import { handleTokenRefresh } from "../LoginRequest";
import {ErrorResponseInterface} from "../../Response/ErrorResponseInterface";
import { apiURL, loginUrl } from "../../Common/CommonURLs";

export function ErrorResponseInterceptor(props: {showErrorAnnouncementFlash: (errors: Array<string>, title: string, timer?: number|null) => void}): void {
    const errorAnnouncementFlash = props.showErrorAnnouncementFlash;

    axios.interceptors.response.use(function (response) {
        const errors: ErrorResponseInterface = response.data;
        if (response.status !== 200 || errors?.errors !== undefined) {
            let errorMessages: Array<string> = [];
            errors?.errors.forEach((error: string) => {
                errorMessages.push(error);
            });
            errorAnnouncementFlash(errorMessages, errors.title);
        } else {
        }

        return response;
    }, async function (error: AxiosError) {
        if (error.response.config.url ===  `${apiURL}token/refresh` && window.location.pathname !==`${loginUrl}`) {        
            window.location.replace(`${loginUrl}`)
        }
        
        console.log('haha error', error.response.data);
        if (typeof error.response.data === 'object' &&  "errors" in error.response.data) {
            console.log('haha error22222', error.response.data);
            const errorResponse: ErrorResponseInterface = error.response.data as ErrorResponseInterface;
            const errorsForModal: Array<string> = errorResponse.errors;

            errorAnnouncementFlash(errorsForModal, 'Error' ?? errorResponse.title );
        } else {
            console.log('here we go', error.response.data);
            if (error.response.status === 401 || error.response.status === 403) {
                const refreshToken: string|null = getRefreshToken();
                console.log('refresh token', refreshToken);
                if (refreshToken !== null) {
                    try {
                        const refreshTokenResponse: AxiosResponse = await handleTokenRefresh();
                    } catch (err) {
                        const error = err as Error | AxiosError;
                    }
                } else {
                    window.location.replace(`${loginUrl}`)
                }
            } 
            if (error.response.status === 500) {
                errorAnnouncementFlash(['Unrecognized issue please log out and back in again'], 'Error');
            }
        }

        return Promise.reject(error);
    });
}
