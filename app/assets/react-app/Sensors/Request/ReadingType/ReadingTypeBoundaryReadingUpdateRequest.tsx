import axios, { AxiosResponse } from 'axios';
import { apiURL } from '../../../Common/URLs/CommonURLs';
import { StandardSensorConstRecord, StandardSensorReadingValue } from '../../Types/StandardSensor/SensorReadingResponseTypes';

export async function readingTypeBoundaryReadingUpdateRequest(
    sensorID: number,
    sensorBoundaryUpdates: StandardSensorBoundaryReadingUpdateInputInterface[]
): Promise<AxiosResponse> {
    const sensorReadingUpdateRequestResponse: AxiosResponse = await axios.put(
        `${apiURL}sensor/${sensorID}/boundary-update`,
        {'sensorData' : sensorBoundaryUpdates},
    );

    return sensorReadingUpdateRequestResponse;
}

export interface StandardSensorBoundaryReadingUpdateInputInterface {
    readingType: string,
    highReading: StandardSensorReadingValue,
    lowReading: StandardSensorReadingValue,
    constRecord: StandardSensorConstRecord,
}