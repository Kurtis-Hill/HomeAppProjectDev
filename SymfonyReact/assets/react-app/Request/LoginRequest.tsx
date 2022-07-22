import axios from 'axios';

import { apiURL } from "../Common/CommonURLs";

import {LoginResponseInterface} from "../Response/Login/Interfaces/LoginResponseInterface";

export default async function handleLogin(userInputs: object): Promise<LoginResponseInterface> {
    const loginCheckResponse = await axios.post(
        `${apiURL}login_check`,
        userInputs
    );

    return loginCheckResponse.data
}
