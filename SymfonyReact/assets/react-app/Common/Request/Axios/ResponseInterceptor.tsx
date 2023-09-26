import { useState, useRef } from 'react';

import axios, {AxiosError, AxiosResponse} from 'axios';
import { NavigateFunction, useNavigate } from "react-router-dom";

import { apiURL, indexUrl } from '../../URLs/CommonURLs';
import { getRefreshToken, removeTokens } from '../../../Authentication/Tokens/GetAPITokens';
import { ErrorResponseInterface } from '../../Response/ErrorResponseInterface';
import { loginUrl } from '../../URLs/CommonURLs';
import { handleTokenRefresh } from '../../../Authentication/Request/LoginRequest';
import { refreshUserTokens } from '../../../Authentication/Session/UserSession';
import { TokenRefreshResponseInterface } from '../../../Authentication/Response/TokenRefreshResponseInterface';

export function ResponseInterceptor(props: {showAnnouncementFlash: (errors: Array<string>, title: string, timer?: number|null) => void}): void {
    const errorAnnouncementFlash = props.showAnnouncementFlash;

    const navigate: NavigateFunction = useNavigate();

    const [responseTokenRequestInProgress, setResponseTokenRequestInProgress] = useState<boolean>(false);

    axios.interceptors.response.use(function (response) {
        // if (response?.data !== undefined) {
        //     const errors: ErrorResponseInterface = response.data;
        //     if (
        //         response.status !== 200
        //         && response.status !== 201
        //         && response.status !== 202
        //         && response.status !== 204
        //         || errors?.errors !== undefined
        //     ) {
        //         let errorMessages: Array<string> = [];
        //         errors?.errors.forEach((error: string) => {
        //             errorMessages.push(error);
        //         });
        //         errorAnnouncementFlash(errorMessages, errors.title);
        //     } else {
                // const requestURL = response.request.responseURL;

                // regex to match the user device id
                // const intRegex = new RegExp(/([0-9]+)/g);
                // const intRegex = new RegExp(/([0-9]+)/g);
                // const intRegexTwo = new RegExp(`/get/`);
                // console.log('anti get', requestURL.search('get'))
                // if (
                //     requestURL.search('get') > 0
                //     // requestURL === `${apiURL}user-devices/${new RegExp(RegExp)}/update`
                //     // requestURL !== `${apiURL}user-data/get`
                //     // && requestURL !== `${apiURL}navbar/navbar-data`
                //     // && requestURL !== `${apiURL}reading-types/all`
                //     // && requestURL !== `${apiURL}sensor-types/all`
                //     // && requestURL !== `${apiURL}sensor-types/all`
                //     // && requestURL !==  `${apiURL}user-device/([0-9]+)`
                // ) {
                //     const payload = response.data.payload;
                //     let successMessages: Array<string> = [];
                //     console.log('payload', payload);
                    
                //     payload?.forEach((message: string) => {
                //         successMessages.push(message);
                //     })
                    // errorAnnouncementFlash(successMessages, 'Success');
                // }
            // }
        // }

        return response;
    }, async function (error: AxiosError|Error) {
        if (error instanceof AxiosError) {
            if (
                error.response.config.url ===  `${apiURL}token/refresh` 
                && window.location.pathname !== `${loginUrl}`
                ) {        
                    window.location.replace(`${loginUrl}`)
                }
                
            if (typeof error.response.data === 'object' &&  "errors" in error.response.data) {
                const errorResponse: ErrorResponseInterface = error.response.data as ErrorResponseInterface;
                const errorsForModal: Array<string> = errorResponse.errors;
                errorAnnouncementFlash(errorsForModal, 'Error' ?? errorResponse.title );
            } else {
                if (error.response.status === 401 || error.response.status === 403 && responseTokenRequestInProgress === false) {
                    setResponseTokenRequestInProgress(true);
                    const refreshToken: string|null = getRefreshToken();
                    if (refreshToken !== null) {
                        try {
                            const refreshTokenResponse: AxiosResponse = await handleTokenRefresh();
                            removeTokens();
                            if (refreshTokenResponse.status === 200) {
                                const refreshTokenResponseData: TokenRefreshResponseInterface = refreshTokenResponse.data;
                                refreshUserTokens(refreshTokenResponseData);
                                setResponseTokenRequestInProgress(false);
                            }
                        } catch (err) {
                            const error = err as Error|AxiosError;
                            alert('Your session has expired please log in again');
setResponseTokenRequestInProgress(false);
                        }
                    } else {
setResponseTokenRequestInProgress(false);
                        window.location.replace(`${loginUrl}`)
                    }
                }
                if (error.response.status === 500) {
                    errorAnnouncementFlash(['Unrecognized issue please log out and back in again'], 'Error');
                }
                // else if (error.response.status === 404) {
                //     navigate(`${indexUrl}`)
                // }
                 else if (error.response.status !== 401) {
                    errorAnnouncementFlash([error.message], 'Error');
                }                 
            }
        } else {            
            errorAnnouncementFlash([error.message], 'Error');
        }
    });
}
