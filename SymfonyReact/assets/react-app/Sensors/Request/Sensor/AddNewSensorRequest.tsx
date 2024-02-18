import axios, {AxiosError, AxiosResponse} from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";
import { NewSensorType } from '../../Components/AddSensor/AddNewSensor';

export async function addNewSensorRequest(newSensorData: NewSensorType): Promise<AxiosResponse> {
    const addNewSensorResponse: AxiosResponse = await axios.post(
        `${apiURL}sensor/add`,
        newSensorData,
    );

    return addNewSensorResponse;
}