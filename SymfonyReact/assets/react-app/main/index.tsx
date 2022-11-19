import * as React from "react";
import * as ReactDOM from "react-dom/client";
import {
    BrowserRouter,
    Routes,
    Route,
    useNavigate,
} from "react-router-dom";

import axios, {AxiosError} from 'axios';
// import '@fortawesome/fontawesome-free/js/fontawesome'
// import '@fortawesome/fontawesome-free/js/solid'
// import '@fortawesome/fontawesome-free/js/regular'
// import '@fortawesome/fontawesome-free/js/brands'

import Login from "../Routes/Login/Login";
import { LandingPage } from "../Routes/LandingPage/LandingPage";

import { MainPageTop } from "../Components/Pages/MainPageTop";

import { getToken, getRefreshToken } from "../Common/APICommon";

import { handleTokenRefresh } from "../Request/LoginRequest";

import {indexUrl, loginUrl } from "../Common/CommonURLs";
import { removeUserSession } from "../Session/UserSession";
import {handlePingRequest} from "../Request/Ping";


axios.interceptors.request.use(
    request => {
        if (!request.url.includes('/HomeApp/api/user/token/refresh')) {
            console.log('adding auth token to request')
            const token = getToken();
            if (token) {
                request.headers['Authorization'] = `Bearer ${token}`;
            }
        }

        return request;
    }
)

export function ErrorResponseInterceptor() {
    axios.interceptors.response.use(undefined, async function (error) {
        if (error.response.status === 401) {
            try {
                const ping = await handlePingRequest();
                console.log('ping me', ping)
            } catch (err) {
                const error = err as AxiosError;
                console.log('error of ping', error)
                if (error.response.status === 403) {
                    const refreshToken: string = getRefreshToken();
                    if (refreshToken) {
                        console.log('before refresh function called')
                        try {
                            const refreshTokenResponse = await handleTokenRefresh();
                            console.log('refresh token response', refreshTokenResponse)
                        } catch (err) {
                            const error = err as Error | AxiosError;
                            console.log('error of refresh', error)
                        }
                    } else {
                        console.log('no refresh token')
                        window.location.replace(`${loginUrl}`);
                    }
                }
            }

        }
        return Promise.reject(error);
    });
}

const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(
    <BrowserRouter>
        <ErrorResponseInterceptor />
        <Routes>
            <Route path="/HomeApp/WebApp/login" element={<Login />}></Route>
            <Route path="/HomeApp/WebApp/" element={<MainPageTop  />}>
                <Route path="index" element={<LandingPage />} />

            </Route>
        </Routes>
    </BrowserRouter>
);
