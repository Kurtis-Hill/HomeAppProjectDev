import * as React from "react";

import axios, { AxiosError } from 'axios';
import { getToken } from "../../Common/APICommon";

export function RequestInterceptor(): void {
    axios.interceptors.request.use(
        request => {
            if (!request.url.includes('/login_check') && !request.url.includes('/token/refresh')) {
                const token = getToken();
                if (token) {
                    request.headers['Authorization'] = `BEARER ${token}`;
                }
            }

            return request;
        }
    )
}
