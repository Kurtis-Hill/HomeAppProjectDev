import axios, {AxiosError, AxiosResponse} from 'axios';

import { apiURL } from "../Common/CommonURLs";
import SensorReadingTypeResponseInterface from './GetSensorReadingTypeRequest';

export async function handleSensorReadingTypesRequest(): Promise<SensorReadingTypeResponseInterface[]|null {
    try {
        const sensorDataResponse: AxiosResponse = await axios.get(
            `${apiURL}all-reading-types`
        );
    
        const sensorReadingTypes: SensorReadingTypeResponseInterface[] = sensorDataResponse.data.payload;
    
        return sensorReadingTypes;
    } catch(err) {
        const error = err as Error | AxiosError;
    }

    return null;
}
