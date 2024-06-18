import axios, {AxiosError, AxiosResponse} from 'axios';
import { NavigateFunction, useNavigate } from "react-router-dom";

import { apiURL } from '../../URLs/CommonURLs';
import { ErrorResponseInterface } from '../../Response/ErrorResponseInterface';
import { loginUrl } from '../../URLs/CommonURLs';
import { handleTokenRefresh } from '../../../Authentication/Request/LoginRequest';
import {getRefreshToken, refreshUserTokens, removeTokens} from '../../../Authentication/Session/UserSessionHelper';
import { TokenRefreshResponseInterface } from '../../../Authentication/Response/TokenRefreshResponseInterface';

export function ResponseInterceptor(props: {
    showAnnouncementFlash: (errors: Array<string>, title: string, timer?: number|null) => void
    refreshNavBar: (newValue: boolean) => void
}): void {
    const errorAnnouncementFlash = props.showAnnouncementFlash;

    // const navigate: NavigateFunction = useNavigate();

    const refreshNavBar = props.refreshNavBar;

    axios.interceptors.response.use(function (response) {
        return response;
    }, async function (error: AxiosError|Error) {
        if (error instanceof AxiosError) {
            const urlOfRequest: string = error.config.url ?? '';
            if (
                urlOfRequest ===  `${apiURL}token/refresh` 
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
             
            if (error.response.status === 401 || error.response.status === 403) {
                if (
                    urlOfRequest !== `${apiURL}token/refresh`
                    && urlOfRequest !== `${apiURL}navbar/navbar-data`
                    && urlOfRequest !== `${apiURL}ping`
                    && urlOfRequest !== `${apiURL}reading-types/all`
                    && urlOfRequest !== `${apiURL}sensor-types/all`
                ) {
                    const refreshToken: string|null = getRefreshToken();
                    if (refreshToken !== null) {
                        try {
                            const refreshTokenResponse: AxiosResponse = await handleTokenRefresh();
                            removeTokens();
                            if (refreshTokenResponse.status === 200) {
                                const refreshTokenResponseData: TokenRefreshResponseInterface = refreshTokenResponse.data;
                                refreshUserTokens(refreshTokenResponseData);
                                refreshNavBar(true);
                            }
                        } catch (err) {
                            const error = err as Error|AxiosError;
                            alert('Your session has expired please log in again');
                        }
                    } else {
                        window.location.replace(`${loginUrl}`)
                    }
                }
            }
            if (error.response.status === 500) {
                errorAnnouncementFlash(['Unrecognized issue please log out and back in again'], 'Error');
            } else if (error.response.status !== 401) {
                errorAnnouncementFlash([error.message], 'Error');
            }                 
            
        } else {            
            errorAnnouncementFlash([error.message], 'Error');
        }
    });
}
