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
        return response;
    }, async function (error: AxiosError|Error) {
        console.log('error', error);
        if (error instanceof AxiosError) {
            if (
                error.config.url ===  `${apiURL}token/refresh` 
                && window.location.pathname !== `${loginUrl}`
                ) {        
                    window.location.replace(`${loginUrl}`)
                }
                
            if (typeof error.response.data === 'object' &&  "errors" in error.response.data) {
                const errorResponse: ErrorResponseInterface = error.response.data as ErrorResponseInterface;
                const errorsForModal: Array<string> = errorResponse.errors;
                errorAnnouncementFlash(errorsForModal, 'Error' ?? errorResponse.title );

                return error;
            }
             
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
                else if (error.response.status !== 401) {
                errorAnnouncementFlash([error.message], 'Error');
            }                 
            
        } else {            
            errorAnnouncementFlash([error.message], 'Error');
        }
    });
}
