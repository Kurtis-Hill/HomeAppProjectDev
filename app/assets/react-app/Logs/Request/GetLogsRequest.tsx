import axios, { AxiosResponse } from 'axios';
import { apiURL } from '../../Common/URLs/CommonURLs';

export interface LogSearchParams {
    keyword?: string;
    level?: string;
    startDate?: string;
    endDate?: string;
    limit?: number;
    offset?: number;
}

export async function getLogsRequest(params: LogSearchParams = {}): Promise<AxiosResponse> {
    return await axios.get(
        `${apiURL}logs`,
        { params },
    );
}
