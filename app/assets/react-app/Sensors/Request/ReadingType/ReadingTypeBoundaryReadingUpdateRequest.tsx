import axios, {AxiosResponse} from 'axios';
import {apiURL} from '../../../Common/URLs/CommonURLs';
import {
    StandardSensorConstRecord,
    StandardSensorReadingValue
} from '../../Types/StandardSensor/SensorReadingResponseTypes';
import {ResponseTypeEnum} from "../../../Common/Response/APIResponseEnum";

export async function readingTypeBoundaryReadingUpdateRequest(
    sensorID: number,
    sensorBoundaryUpdates: StandardSensorBoundaryReadingUpdateInputInterface[]
): Promise<AxiosResponse> {
    return await axios.put(
        `${apiURL}sensor/${sensorID}/boundary-update?responseType=${ResponseTypeEnum.ResponseTypeFull}`,
        {'sensorData': sensorBoundaryUpdates},
    );
}

export interface StandardSensorBoundaryReadingUpdateInputInterface {
    readingType: string,
    highReading: StandardSensorReadingValue,
    lowReading: StandardSensorReadingValue,
    constRecord: StandardSensorConstRecord,
}
