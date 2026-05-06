import * as React from 'react';
import SensorResponseInterface from '../../Response/Sensor/SensorResponseInterface';
import { RelayUpdateRequestCommand } from '../Commands/RelayUpdateRequestCommand';
import { PingDevice } from '../../../Devices/Components/Buttons/PingDevice';
import { RestartDeviceButton } from '../../../Devices/Components/Buttons/RestartDeviceButton';
import { SensorTypesEnum } from '../../Enum/SensorTypesEnum';

export function CommandsDisplay(props: { sensor: SensorResponseInterface }) {
    const { sensor } = props;
    
    return (
        <>
            <PingDevice
                deviceID={sensor?.device?.deviceID ?? 0}
            />
            <br />
            <RestartDeviceButton
                deviceID={sensor?.device?.deviceID ?? 0}
            />
            <br />
            {
                sensor?.sensorType?.sensorTypeName === SensorTypesEnum.GenericRelay
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
