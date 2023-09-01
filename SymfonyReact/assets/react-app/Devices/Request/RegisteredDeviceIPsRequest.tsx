import axios, { Axios, AxiosResponse } from 'axios';
import { IPLogResponseInterface } from '../../Common/Response/IPLogResponseInterface';
import { apiURL } from '../../Common/URLs/CommonURLs';


export async function registeredDeviceIPsRequest(): Promise<AxiosResponse> {
    const registeredDeviceIPsResponse: AxiosResponse = await axios.get(
        `${apiURL}registered-devices`,
    );

    return registeredDeviceIPsResponse;
}