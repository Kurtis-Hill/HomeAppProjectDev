import * as React from 'react';
import { useState, useEffect, useMemo } from 'react';
import { useParams } from "react-router-dom";

import { getDeviceRequest, DeviceResponseInterface } from '../../Devices/Request/GetDeviceRequest';
import DotCircleSpinner from '../../Common/Components/Spinners/DotCircleSpinner';
import { UpdateDevice } from '../../Devices/Components/UpdateDevice/UpdateDevice';

export function DevicePage() {
    const params = useParams();

    const deviceID = params.deviceID;

    const [deviceData, setDeviceData] = useState<DeviceResponseInterface|null>(null);

    const [deviceLoading, setDeviceLoading] = useState<boolean>(true);

    const getDeviceData = async () => {
        const getDeviceResponse = await getDeviceRequest(parseInt(deviceID), 'full');
        const deviceData: DeviceResponseInterface = getDeviceResponse.data.payload;
        setDeviceData(deviceData);
        setDeviceLoading(false);
        console.log('deviceData', deviceData)
    }

    useMemo(() => {
        getDeviceData();
    }, [deviceID]);


    if (deviceLoading === true) {
        return <DotCircleSpinner spinnerSize={5} classes="center-spinner" />
    }

    return (
        <>
            <UpdateDevice
                deviceID={deviceData.deviceID}
                deviceName={deviceData.deviceName}
                groupName={deviceData.group}
                room={deviceData.room}
                roles={deviceData.roles}
            />
        </>
    );
}