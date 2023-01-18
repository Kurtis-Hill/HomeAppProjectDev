import * as React from 'react';
import { useState, useEffect } from 'react';

import { sensorTypesRequest as sensorTypesRequest } from '../SensorType/Request/GetSensorTypeRequest';
import { sensorReadingTypesRequest } from '../ReadingType/Request/GetSensorReadingTypeRequest';

import { SensorTypeResponseInterface } from '../SensorType/Response/SensorTypeResponseInterface';
import { SensorReadingTypeResponseInterface } from '../ReadingType/Response/SensorReadingTypeResponseInterface';

import SensorDataContext from '../Contexts/SensorDataContext';

export function SensorDataContextProvider({ children }) {
    const [sensorReadingTypeData, setSensorReadingTypeData] = useState<SensorReadingTypeResponseInterface[]|[]>([]);
    
    const [sensorTypes, setSensorTypes] = useState<SensorTypeResponseInterface[]|[]>([]);
    
    useEffect(() => {
        handleSensorDataRequest();
    }, []);

    const handleSensorDataRequest = async () => {
        console.log('handleSensorDataRequest');
        if (sensorReadingTypeData.length === 0) {             
            const sensorReadingTypes = await sensorReadingTypesRequest();
            if (sensorReadingTypes !== null) {
                setSensorReadingTypeData(sensorReadingTypes);
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
                sensorReadingTypeData
            }
        }
        >
            { children }
        </SensorDataContext.Provider>
    );
}

export interface SensorDataContextDataInterface {
    sensorTypes: SensorTypeResponseInterface[]|[];
    sensorReadingTypeData: SensorReadingTypeResponseInterface[]|[];
}