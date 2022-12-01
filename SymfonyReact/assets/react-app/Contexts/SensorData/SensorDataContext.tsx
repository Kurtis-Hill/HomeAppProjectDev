import * as React from 'react';
import { useState, useEffect } from 'react';
import { Component, createContext } from 'react';

import { handleSensorTypesRequest } from '../../Request/GetSensorTypeRequest';
import { handleSensorReadingTypesRequest } from '../../Request/GetSensorReadingTypeRequest';

import { SensorTypeResponseInterface } from '../../Response/Sensor/SensorTypeResponseInterface';
import { SensorReadingTypeResponseInterface } from '../../Response/Sensor/SensorReadingTypeResponseInterface';

export const SensorDataContext = createContext();

function SensorDataContextProvider({ children }) {

    const [sensorReadingTypeData, setSensorReadingTypeData] = useState<SensorReadingTypeResponseInterface[]|[]>([]);
    
    const [sensorTypes, setSensorTypes] = useState<SensorTypeResponseInterface[]|[]>([]);
    
    useEffect(() => {
        handleSensorDataRequest();
    }, []);

    const useSensorDataContext() {
        const context = React.useContext(SensorDataContext);
        if (context === undefined) {
            throw new Error('useSensorDataContext must be used within a SensorDataContextProvider');
        }
    
        return context;
    }
    
    function SensorDataConsumer({children}) {
        <SensorDataContext.Consumer>
            {context => {
                if (context === undefined) {
                    throw new Error('useSensorDataContext must be used within a SensorDataContextProvider');
                }
                return children(context)
            }}
        </SensorDataContext.Consumer>
    }

    const handleSensorDataRequest = async () => {
        console.log('handleSensorDataRequest');
        if (
            this.state.sensorReadingTypes.length === 0
            || this.state.sensorTypes.length === 0
        ) {             
                const sensorTypesResponse = await handleSensorTypesRequest();
                if (sensorTypesResponse !== null) {
                    setSensorTypes()
                }
                const sensorReadingTypes = await handleSensorReadingTypesRequest();
                if (sensorReadingTypes !== null) {
                    setSensorReadingTypeData(sensorReadingTypes);
                } 
        }
    }

    return (
        // <>
            <SensorDataContext.Provider 
                sensorTypes= {sensorTypes}
                sensorReadingTypeData= {sensorReadingTypeData}
            >
                {children}
            </SensorDataContext.Provider>
        // </>
    );
}



export { SensorDataContextProvider };