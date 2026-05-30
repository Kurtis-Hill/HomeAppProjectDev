import * as React from 'react';
import { useState } from 'react';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';
import SensorResponseInterface from '../../Response/Sensor/SensorResponseInterface';
import { switchSensorRequest } from '../../Request/Sensor/SwitchSensorRequest';
import {SensorTypesEnum} from "../../Enum/SensorTypesEnum";

export function RelayUpdateRequestCommand(props: { sensor: SensorResponseInterface }) {
    const { sensor } = props;
    
    const [relayState, setRelayState] = useState<boolean>(sensor.sensorReadingTypes?.relay?.currentReading ?? false);

    const handleRelayUpdate = async (e: Event) => {
        const target = e.target as HTMLInputElement;
        const value = target.checked as boolean;

        setRelayState((currentState: boolean) => value)            
        
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

    if (sensor?.sensorType?.sensorTypeName === SensorTypesEnum.GenericRelay) {
        const shouldBeChecked = relayState;
        // const disabled = sensor.sensorReadingTypes.relay.currentReading === true !== sensor.sensorReadingTypes.relay.requestedReading;
        const disabled = false;

        return (
            <div className="relay-control">
                <span className="relay-control-label">Relay</span>
                <div className="relay-control-row">
                    { disabled ? <DotCircleSpinner classes="center-spinner bool-sensor-button-loading" /> : null }
                    <label className="switch-lg">
                        <input
                            type="checkbox"
                            onChange={(e: React.ChangeEvent<HTMLInputElement>) => handleRelayUpdate(e as unknown as Event)}
                            checked={shouldBeChecked}
                            disabled={disabled}
                        />
                        <span className="slider round"></span>
                    </label>
                    <span className={`relay-status ${shouldBeChecked ? 'relay-status-on' : 'relay-status-off'}`}>
                        {shouldBeChecked ? 'ON' : 'OFF'}
                    </span>
                </div>
            </div>
        )
    }
}
