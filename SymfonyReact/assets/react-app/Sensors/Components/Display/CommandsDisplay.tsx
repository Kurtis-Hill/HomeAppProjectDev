import * as React from 'react';
import { useState, useEffect } from 'react';
import SensorResponseInterface from '../../Response/Sensor/SensorResponseInterface';
import { SensorTypesEnum } from '../../../Enum/SensorTypesEnum';
import { capitalizeFirstLetter } from '../../../Common/StringFormatter';
import { switchSensorRequest } from '../../Request/Sensor/SwitchSensorRequest';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';
import { RelayUpdateRequestCommand } from '../Commands/RelayUpdateRequestCommand';

export function CommandsDisplay(props: { sensor: SensorResponseInterface }) {
    const { sensor } = props;
    
    // if (sensor.sensorType.sensorTypeName === SensorTypesEnum.GenericRelay) {
        // const shouldBeChecked = relayState;;
        // const disabled = sensor.sensorReadingTypes.relay.currentReading === true !== sensor.sensorReadingTypes.relay.requestedReading;
        // const disabled = false;

        return (
            <>       
                {
                    sensor.sensorType.sensorTypeName === SensorTypesEnum.GenericRelay
                        ?
                            <RelayUpdateRequestCommand
                                sensor={sensor}
                            />      
                        :
                            null
                }
                {/* <h5 className="title">Send Relay Update Request</h5>
                <label className="switch">
                    { disabled ? <DotCircleSpinner classes="center-spinner bool-sensor-button-loading"  /> : null }
                    <input 
                        type="checkbox"
                        onChange={(e: Event) => handleRelayUpdate(e)}
                        checked={shouldBeChecked}
                        disabled={disabled}
                    />
                    <span className="slider round"></span>
                </label>  */}
            </>
        )
    
    
    return (
        <>
            <div>
                <h5 className="title">Nothing to display</h5>
            </div>
        </>
    );
}
