import * as React from 'react';
import { useState, useEffect } from 'react';

import { handleSensorTypesRequest } from '../../Request/GetSensorTypeRequest';
import { handleSensorReadingTypesRequest } from '../../Request/GetSensorReadingTypeRequest';

import { SensorTypeResponseInterface } from '../../Response/Sensor/SensorTypeResponseInterface';
import { SensorReadingTypeResponseInterface } from '../../Response/Sensor/SensorReadingTypeResponseInterface';

import SensorDataContext from '../../Contexts/SensorData/SensorDataContext';

// export const SensorDataContext = createContext();

export function SensorDataContextProvider({ children }) {

    const [sensorReadingTypeData, setSensorReadingTypeData] = useState<SensorReadingTypeResponseInterface[]|[]>([]);
    
    const [sensorTypes, setSensorTypes] = useState<SensorTypeResponseInterface[]|[]>([]);
    
    useEffect(() => {
        handleSensorDataRequest();
    }, []);

    // const useSensorDataContext = () => {
    //     const context = React.useContext(SensorDataContext);
    //     if (context === undefined) {
    //         throw new Error('useSensorDataContext must be used within a SensorDataContextProvider');
    //     }
    
    //     return context;
    // }

    
    // function SensorDataConsumer({children}) {
    //     <SensorDataContext.Consumer>
    //         {context => {
    //             if (context === undefined) {
    //                 throw new Error('useSensorDataContext must be used within a SensorDataContextProvider');
    //             }
    //             return children(context)
    //         }}
    //     </SensorDataContext.Consumer>
    // }

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
                setSensorTypes()
            }
        }
    }

    return (
        // <>
            <SensorDataContext.Provider value={ 
                {
                    sensorTypes,
                    sensorReadingTypeData
                }
            }
            >
                {children}
            </SensorDataContext.Provider>
        // </>
    );
}

    // const withUser = (Child) => (props) => (
    //     <SensorDataContext.Consumer>
    //       {(context) => <Child {...props} {...context} />}
    //       {/* Another option is:  {context => <Child {...props} context={context}/>}*/}
    //     </SensorDataContext.Consumer>
    //   );



// export { SensorDataContextProvider };
// export { SensorDataContextProvider,
//     //  withUser
//      };