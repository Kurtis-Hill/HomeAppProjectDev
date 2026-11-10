import * as React from 'react';
import { useState, useEffect, useCallback } from 'react';

import { sensorTypesRequest as sensorTypesRequest } from '../Request/SensorType/GetSensorTypeRequest';
import { sensorReadingTypesRequest } from '../Request/ReadingType/GetSensorReadingTypeRequest';
import { getSensorsRequest } from '../Request/Sensor/GetSensorsRequest';
import { RequestTypeEnum } from '../../Common/Request/RequestTypeEnum';

import { SensorTypeResponseInterface } from '../Response/SensorType/SensorTypeResponseInterface';

import SensorDataContext from '../Contexts/SensorDataContext';
import ReadingTypeResponseInterface from '../Response/ReadingTypes/ReadingTypeResponseInterface';
import SensorResponseInterface from '../Response/Sensor/SensorResponseInterface';

export function SensorDataContextProvider({ children }) {
    const [readingTypeData, setReadingTypeData] = useState<ReadingTypeResponseInterface[]|[]>([]);
    const [sensorTypes, setSensorTypes] = useState<SensorTypeResponseInterface[]|[]>([]);
    const [allSensors, setAllSensors] = useState<SensorResponseInterface[]>([]);

    useEffect(() => {
        handleSensorDataRequest();
        loadAllSensors();
    }, []);

    const handleSensorDataRequest = async () => {
        if (readingTypeData.length === 0) {             
            const sensorReadingTypesResponse = await sensorReadingTypesRequest();
            const sensorReadingTypes: ReadingTypeResponseInterface[] = sensorReadingTypesResponse.data.payload;
            if (sensorReadingTypes !== null) {
                setReadingTypeData(sensorReadingTypes);
            } 
        }
        if (sensorTypes.length === 0) {
            const sensorTypesResponse = await sensorTypesRequest();
            const sensorTypes: SensorTypeResponseInterface = sensorTypesResponse.data.payload;
            if (sensorTypes !== null) {
                setSensorTypes(sensorTypes)
            }
        }
    }

    const loadAllSensors = useCallback(async () => {
        try {
            const response = await getSensorsRequest({
                limit: 100,
                page: 1,
                deviceIDs: [],
                deviceNames: [],
                cardViewIDs: [],
                responseType: RequestTypeEnum.ONLY,
            });
            if (response.status === 200 && Array.isArray(response.data.payload)) {
                setAllSensors(response.data.payload);
            }
        } catch {
            // silently ignore — sensors list will be empty
        }
    }, []);

    return (
        <SensorDataContext.Provider value={ 
            {
                sensorTypes,
                readingTypes: readingTypeData,
                allSensors,
                refreshSensors: loadAllSensors,
            }
        }
        >
            { children }
        </SensorDataContext.Provider>
    );
}

export interface SensorDataContextDataInterface {
    sensorTypes: SensorTypeResponseInterface[]|[];
    readingTypes: ReadingTypeResponseInterface[]|[];
    allSensors: SensorResponseInterface[];
    refreshSensors: () => Promise<void>;
}
