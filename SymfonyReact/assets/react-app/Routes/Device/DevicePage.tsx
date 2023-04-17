import * as React from 'react';
import { useState, useEffect } from 'react';
import { useParams } from "react-router-dom";

import { getDeviceRequest, DeviceResponseInterface } from '../../Devices/Request/GetDeviceRequest';
export function DevicePage() {
    const params = useParams();
    const deviceID = params.deviceID;

    const [deviceData, setDeviceData] = useState<DeviceResponseInterface|null>(null);

    const getDeviceData = async () => {
        const getDeviceResponse = await getDeviceRequest(parseInt(deviceID));
        const deviceData: DeviceResponseInterface = getDeviceResponse.data.payload;
        setDeviceData(deviceData);
        console.log('deviceData', deviceData)
    }

    useEffect(() => {
        getDeviceData();

    }, []);

    return (
        <>
            <h1>Device Page</h1>
            <h2>Device ID: {deviceID}</h2>
        </>
    );
}