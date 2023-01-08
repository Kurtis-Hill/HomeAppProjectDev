import * as React from 'react';
import { useState, useEffect } from 'react';

import { handleSensorTypesRequest } from '../../Request/GetSensorTypeRequest';
import { handleSensorReadingTypesRequest } from '../../Request/GetSensorReadingTypeRequest';

import { SensorTypeResponseInterface } from '../../Response/Sensor/SensorTypeResponseInterface';
import { SensorReadingTypeResponseInterface } from '../../Response/Sensor/SensorReadingTypeResponseInterface';

import SensorDataContext from '../../Contexts/SensorData/SensorDataContext';

export function SensorDataContextProvider({ children }) {

    const [sensorReadingTypeData, setSensorReadingTypeData] = useState<SensorReadingTypeResponseInterface[]|[]>([]);
    
    const [sensorTypes, setSensorTypes] = useState<SensorTypeResponseInterface[]|[]>([]);
    
    useEffect(() => {
        handleSensorDataRequest();
    }, []);

    const handleSensorDataRequest = async () => {
        console.log('handleSensorDataRequest');
        if (sensorReadingTypeData.length === 0) {             
            const sensorReadingTypes = await handleSensorReadingTypesRequest();
            if (sensorReadingTypes !== null) {
                setSensorReadingTypeData(sensorReadingTypes);
            } 
        }
        if (sensorTypes.length === 0) {
            const sensorTypesResponse = await handleSensorTypesRequest();
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
            {children}
        </SensorDataContext.Provider>
    );
}

export interface SensorDataContextDataInterface {
    sensorTypes: SensorTypeResponseInterface[]|[];
    sensorReadingTypeData: SensorReadingTypeResponseInterface[]|[];
}