import * as React from 'react';
import { useState, useEffect } from 'react';
import SensorResponseInterface from '../../Response/Sensor/SensorResponseInterface';
import { SensorTypesEnum } from '../../../Enum/SensorTypesEnum';
import { capitalizeFirstLetter } from '../../../Common/StringFormatter';
import { switchSensorRequest } from '../../Request/Sensor/SwitchSensorRequest';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';

export function CommandsDisplay(props: { sensor: SensorResponseInterface }) {
    const { sensor } = props;
    
    const [relayState, setRelayState] = useState<boolean>(sensor.sensorReadingTypes.relay.requestedReading);


    const handleRelayUpdate = async () => {
        
        setRelayState((currentState: boolean) => !currentState);
        const switchSensorResponse = await switchSensorRequest({
            'sensorData': [
                {
                    'sensorName': sensor.sensorName,
                    'currentReadings': 
                    {
                        'relay': !relayState,
                    }
                    
                }
            ]
        })

        if (switchSensorResponse.status !== 202) {
            setRelayState((currentState: boolean) => !currentState);
        }
    }


    if (sensor.sensorType.sensorTypeName === SensorTypesEnum.GenericRelay) {         
        // const shouldBeChecked = sensor.sensorReadingTypes.relay.currentReading === true || sensor.sensorReadingTypes.relay.requestedReading === true || relayState === true;               
        const disabled = (sensor.sensorReadingTypes.relay.currentReading !== sensor.sensorReadingTypes.relay.requestedReading) || (sensor.sensorReadingTypes.relay.requestedReading !== relayState);

        return (
            <>                       
                <h5 className="title">Send Relay Update Request</h5>
                <label className="switch">
                    { disabled ? <DotCircleSpinner classes="center-spinner bool-sensor-button-loading"  /> : null }
                    <input 
                        type="checkbox"
                        onChange={() => handleRelayUpdate()}
                        checked={relayState}
                        disabled={disabled}
                    />
                    <span className="slider round"></span>
                </label> 
            </>
        )
    }
    
    return (
        <>
            <div>
                <h5 className="title">Nothing to display</h5>
            </div>
        </>
    );
}