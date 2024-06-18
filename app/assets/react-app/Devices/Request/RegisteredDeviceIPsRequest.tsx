import axios, {AxiosResponse} from 'axios';
import {apiURL} from '../../Common/URLs/CommonURLs';


export async function registeredDeviceIPsRequest(): Promise<AxiosResponse> {
    return await axios.get(
        `${apiURL}registered-devices`,
    );
}
