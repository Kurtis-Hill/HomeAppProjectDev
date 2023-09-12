import * as React from 'react';
import { useState, useEffect } from 'react';
import SensorResponseInterface from '../../Response/Sensor/SensorResponseInterface';
import { SensorTypesEnum } from '../../../Enum/SensorTypesEnum';
import { capitalizeFirstLetter } from '../../../Common/StringFormatter';
import { switchSensorRequest } from '../../Request/Sensor/SwitchSensorRequest';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';

export function CommandsDisplay(props: { sensor: SensorResponseInterface }) {
    const { sensor } = props;
    
    const [relayState, setRelayState] = useState<boolean>(sensor.sensorReadingTypes.relay.currentReading);

    console.log('before click', relayState);
    const handleRelayUpdate = async (e: Event) => {
        const target = e.target as HTMLInputElement;
        const value = target.checked as boolean;

        setRelayState((currentState: boolean) => value)            
        
        console.log('current state', relayState);
        const switchSensorResponse = await switchSensorRequest({
            'sensorData': [
                {
                    'sensorName': sensor.sensorName,
                    'currentReadings': 
                        {
                            'relay': value,
                        }
                        
                }
            ]
        })
        
        if (switchSensorResponse.status !== 202) {
            setRelayState((currentState: boolean) => !value)
        }
    }

    if (sensor.sensorType.sensorTypeName === SensorTypesEnum.GenericRelay) {
        const shouldBeChecked = relayState;;
        // const disabled = sensor.sensorReadingTypes.relay.currentReading === true !== sensor.sensorReadingTypes.relay.requestedReading;
        const disabled = false;

        return (
            <>                       
                <h5 className="title">Send Relay Update Request</h5>
                <label className="switch">
                    { disabled ? <DotCircleSpinner classes="center-spinner bool-sensor-button-loading"  /> : null }
                    <input 
                        type="checkbox"
                        onChange={(e: Event) => handleRelayUpdate(e)}
                        checked={shouldBeChecked}
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