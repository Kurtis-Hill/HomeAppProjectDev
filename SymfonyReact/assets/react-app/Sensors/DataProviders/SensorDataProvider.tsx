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
        console.log('handleSensorDataRequest');
        if (readingTypeData.length === 0) {             
            const sensorReadingTypes: ReadingTypeResponseInterface[] = await sensorReadingTypesRequest();
            if (sensorReadingTypes !== null) {
                console.log('here we are', sensorReadingTypes);
                setReadingTypeData(sensorReadingTypes);
            } 
        }
        if (sensorTypes.length === 0) {
            const sensorTypesResponse = await sensorTypesRequest();
            if (sensorTypesResponse !== null) {
                setSensorTypes(sensorTypesResponse)
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