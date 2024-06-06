import * as React from 'react';
import { useState, useEffect } from 'react';

import { sensorTypesRequest as sensorTypesRequest } from '../Request/SensorType/GetSensorTypeRequest';
import { sensorReadingTypesRequest } from '../Request/ReadingType/GetSensorReadingTypeRequest';

import { SensorTypeResponseInterface } from '../Response/SensorType/SensorTypeResponseInterface';

import SensorDataContext from '../Contexts/SensorDataContext';
import ReadingTypeResponseInterface from '../Response/ReadingTypes/ReadingTypeResponseInterface';

export function SensorDataContextProvider({ children }) {
    const [readingTypeData, setReadingTypeData] = useState<ReadingTypeResponseInterface[]|[]>([]);
    
    const [sensorTypes, setSensorTypes] = useState<SensorTypeResponseInterface[]|[]>([]);
    
    useEffect(() => {
        handleSensorDataRequest();
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

    return (
        <SensorDataContext.Provider value={ 
            {
                sensorTypes,
                readingTypes: readingTypeData
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
}