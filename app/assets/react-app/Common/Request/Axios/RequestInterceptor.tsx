import axios from 'axios';
import {getToken} from "../../../Authentication/Session/UserSessionHelper";

export function RequestInterceptor(): void {
    axios.interceptors.request.use(
        request => {
            if (!request.url.includes('/login_check') && !request.url.includes('/token/refresh')) {
                const token: string|null = getToken();
                if (token !== null) {
                    request.headers['Authorization'] = `BEARER ${token}`;
                }
            }

            return request;
        }
    )
}
