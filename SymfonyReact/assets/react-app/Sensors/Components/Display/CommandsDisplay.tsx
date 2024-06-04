import * as React from 'react';
import SensorResponseInterface from '../../Response/Sensor/SensorResponseInterface';
import { SensorTypesEnum } from '../../../Enum/SensorTypesEnum';
import { RelayUpdateRequestCommand } from '../Commands/RelayUpdateRequestCommand';
import { PingDeviceCommand } from '../../../Devices/Components/Command/PingDeviceCommand';
import { RestartDeviceCommand } from '../../../Devices/Components/Command/RestartDeviceCommand';

export function CommandsDisplay(props: { sensor: SensorResponseInterface }) {
    const { sensor } = props;
    
    return (
        <>
            <PingDeviceCommand
                deviceID={sensor.device.deviceID}
            />
            <br />
            <RestartDeviceCommand
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
}
