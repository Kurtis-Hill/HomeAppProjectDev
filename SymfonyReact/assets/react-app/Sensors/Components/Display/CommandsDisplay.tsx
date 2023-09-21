import * as React from 'react';
import SensorResponseInterface from '../../Response/Sensor/SensorResponseInterface';
import { SensorTypesEnum } from '../../../Enum/SensorTypesEnum';
import { capitalizeFirstLetter } from '../../../Common/StringFormatter';
import { switchSensorRequest } from '../../Request/Sensor/SwitchSensorRequest';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';
import { RelayUpdateRequestCommand } from '../Commands/RelayUpdateRequestCommand';
import { PingDeviceCommand } from '../../../Devices/Components/Command/PingDeviceCommand';

export function CommandsDisplay(props: { sensor: SensorResponseInterface }) {
    const { sensor } = props;
    
        return (
            <>   
                <PingDeviceCommand 
                    deviceID={sensor.device.deviceID}
                />    
                <br />
                {
                    sensor.sensorType.sensorTypeName === SensorTypesEnum.GenericRelay
                        ?
                            <RelayUpdateRequestCommand
                                sensor={sensor}
                            />      
                        :
                            null
                }
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
