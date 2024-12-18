import axios, {AxiosResponse} from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";
import { SensorPatchRequestInputInterface } from '../../Response/Sensor/SensorPatchRequestInputInterface';

export async function updateSensorRequest(sensorID: number, sensorUpdateFormInputs: SensorPatchRequestInputInterface, responseType: string = 'only'): Promise<AxiosResponse> {
    const updateSensorResponse: AxiosResponse = await axios.patch(
        `${apiURL}sensor/${sensorID}?${responseType}`,
        sensorUpdateFormInputs,
    );

    return updateSensorResponse;
}
