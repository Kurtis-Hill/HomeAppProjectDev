import * as React from 'react';
import { useState } from 'react';
import SensorResponseInterface from '../../Response/Sensor/SensorResponseInterface';
import { SensorTypesEnum } from '../../../Enum/SensorTypesEnum';
import { capitalizeFirstLetter } from '../../../Common/StringFormatter';

export function CommandsDisplay(props: { sensor: SensorResponseInterface }) {
    const { sensor } = props;

    console.log('sensor', sensor)
    if (sensor.sensorType.sensorTypeName === SensorTypesEnum.GenericRelay) {
        const relaySensorData = sensor.sensorReadingTypes.relay;
        
        const [relayState, setRelayState] = useState<boolean>(false);
                
        return (
            <>                        
                <div className="custom-control custom-switch">
                    <label className="custom-control-label" htmlFor="onOffControl">{relaySensorData.currentReading === true ? 'ON' : 'OFF'}</label>
                    <input 
                        type="checkbox" 
                        className="custom-control-input" 
                        id="onOffControl"
                        checked={relayState === true ? true : false}
                        onChange={() => setRelayState((currentState: boolean) => !currentState)}>
                    </input>
                </div>
            </>
        )
    }
    
    return (
        <>
            <div>

            </div>
        </>
    );
}