import axios, { AxiosResponse } from 'axios';
import { apiURL } from '../../../Common/URLs/CommonURLs';

export async function deleteUserRequest(userID: number): Promise<AxiosResponse> {
    return axios.delete(`${apiURL}${userID}`);
}
