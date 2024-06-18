import axios, { AxiosResponse} from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";
import SensorResponseInterface from '../../Response/Sensor/SensorResponseInterface';

export async function deleteSensorRequest(sensorID: number, responseType: string = 'only'): Promise<SensorResponseInterface|null> {
    const deleteSensorResponse: AxiosResponse = await axios.delete(
        `${apiURL}sensor/${sensorID}/delete?${responseType}`,
    );

    if (deleteSensorResponse.status === 200) {
        const deletedSensor: SensorResponseInterface = deleteSensorResponse.data.payload;
        
        return deletedSensor;
    } else {
        throw Error('Error in deleteSensorRequest');
    }
}