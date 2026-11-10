import axios, { AxiosResponse } from 'axios';
import { apiURL } from '../../../Common/URLs/CommonURLs';

export async function addUserToHomeGroupRequest(userID: number): Promise<AxiosResponse> {
    return axios.post(`${apiURL}${userID}/home-group`);
}
