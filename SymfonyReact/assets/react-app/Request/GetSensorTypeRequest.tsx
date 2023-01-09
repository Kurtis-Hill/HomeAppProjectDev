import axios, {AxiosError, AxiosResponse} from 'axios';

import { apiURL } from "../Common/CommonURLs";
import { SensorTypeResponseInterface } from '../Response/Sensor/SensorTypeResponseInterface';

export async function handleSensorTypesRequest(): Promise<SensorTypeResponseInterface[]|null> {
    try {
        const sensorDataResponse: AxiosResponse = await axios.get(
            `${apiURL}sensor-types/all`
        );

        if (sensorDataResponse.status === 200) {    
            const sensorTypes: SensorTypeResponseInterface[] = sensorDataResponse.data.payload;
            
            return sensorTypes;
        } else {
            throw Error('Error in handleSensorTypesRequest');
        }
    
    } catch(err) {
        const error = err as Error | AxiosError;
    }

    return null;
}