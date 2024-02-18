import axios, { AxiosError, AxiosResponse } from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";
import ReadingTypeResponseInterface from '../../Response/ReadingTypes/ReadingTypeResponseInterface';

export async function sensorReadingTypesRequest(): Promise<ReadingTypeResponseInterface[] | null> {
    try {
        const sensorDataResponse: AxiosResponse = await axios.get(
            `${apiURL}reading-types/all`
        );

        if (sensorDataResponse.status === 200) {
            const sensorReadingTypes: ReadingTypeResponseInterface[] = sensorDataResponse.data.payload;
            
            return sensorReadingTypes;
        } else {
            throw Error('Error in handleSensorReadingTypesRequest');
        }
    } catch (err) {
        const error = err as Error | AxiosError;
    }

    return null;
}
