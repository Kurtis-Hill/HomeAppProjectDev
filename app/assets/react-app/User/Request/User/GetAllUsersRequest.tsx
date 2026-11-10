import axios, { AxiosResponse } from 'axios';
import { apiURL } from '../../../Common/URLs/CommonURLs';

export async function getAllUsersRequest(): Promise<AxiosResponse> {
    return axios.get(`${apiURL}`);
}
