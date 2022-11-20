import * as React from "react";

import axios, {AxiosError, AxiosResponse} from 'axios';

import { getRefreshToken } from "../../Common/APICommon";

import { handleTokenRefresh } from "../LoginRequest";
import {ErrorResponseInterface} from "../../Response/ErrorResponseInterface";

export function ErrorResponseInterceptor(props: {showErrorAnnouncementFlash: (errors: Array<string>, title: string, timer?: number|null) => void}): void {
    const errorAnnouncementFlash = props.showErrorAnnouncementFlash;

    axios.interceptors.response.use(function (response) {
        console.log('response interceptor triggered', response);
        const errors: ErrorResponseInterface = response.data;
        console.log('response interceptor errors', errors);
        if (response.status !== 200 || errors?.errors !== undefined) {
            console.log('response interceptor errors', errors);
            let errorMessages: Array<string> = [];
            errors?.errors.forEach((error: string) => {
                errorMessages.push(error);
            });
            console.log('errors for modal and errors', errorMessages, errors);
            errorAnnouncementFlash(errorMessages, errors.title);
        } else {
            console.log('no error property found')
        }

        return response;
    }, async function (error: AxiosError) {
        console.log('error response interceptor!!!!!!!!!!!!!!!!!!!!!!!!')
        if ("errors" in error) {
            console.log('errors are defined undefined');
            const errorResponse: ErrorResponseInterface = error.response.data;

            let errorsForModal: Array<string>;
            if (typeof errorResponse.errors ===  "string") {
                errorsForModal = [errorResponse.errors];
            } else {
                errorsForModal = errorResponse.errors;
            }
            errorAnnouncementFlash(errorsForModal, 'Error');
        } else {
            console.log('undefined');
            let errorsOverride: boolean = false;

            if (error.response.status === 401) {
                errorsOverride = true
            }
            console.log('error status', error.response.status);
            errorAnnouncementFlash(
                errorsOverride === true
                    ? ['Unauthorized']
                    : ["Unrecognised error"],
                errorsOverride === true
                    ? 'Refreshing user session'
                    : 'Error'
            );
        }

        if (error.response.status === 401 || error.response.status === 403) {
            const refreshToken: string|null = getRefreshToken();
            console.log('refresh token found', refreshToken)
            if (refreshToken !== null) {
                console.log('before refresh function called')
                try {
                    const refreshTokenResponse: AxiosResponse = await handleTokenRefresh();
                    console.log('refresh token response', refreshTokenResponse)
                } catch (err) {
                    const error = err as Error | AxiosError;
                    console.log('error of refresh', error)
                }
            }
        }

        return Promise.reject(error);
    });
}
