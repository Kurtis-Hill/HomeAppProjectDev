import axios, {AxiosError, AxiosResponse} from 'axios';

import { apiURL } from "../Common/CommonURLs";
import { SensorReadingTypeResponseInterface } from '../Response/Sensor/SensorReadingTypeResponseInterface';

export async function handleSensorReadingTypesRequest(): Promise<SensorReadingTypeResponseInterface[]|null {
    try {
        const sensorDataResponse: AxiosResponse = await axios.get(
            `${apiURL}all-reading-types`
        );
        console.log('sensorDataResponse', sensorDataResponse);
    
        if (sensorDataResponse.status === 200) {
            const sensorReadingTypes: SensorReadingTypeResponseInterface[] = sensorDataResponse.data.payload;
         
            return sensorReadingTypes;
        } else {
            throw Error('Error in handleSensorReadingTypesRequest');
        }    
    } catch(err) {
        const error = err as Error | AxiosError;
    }

    return null;
}
