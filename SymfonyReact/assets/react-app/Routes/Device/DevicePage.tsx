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

    const devicePage = () => {
        return (
            <UpdateDevice
                deviceNameID={deviceData.deviceNameID}
                deviceName={deviceData.deviceName}
                groupName={deviceData.groupName}
                room={deviceData.room}
                roles={deviceData.roles}
                
            />
            // Add new sensor to device
            // table showing which ones you do/ do not have cards for
            // see the cards you have for the device and what state there in
        );
    }

    return (
        <>
            {
                deviceLoading === true
                    ? <DotCircleSpinner spinnerSize={5} classes="center-spinner" />
                    : devicePage()
            }
        </>
    );
}